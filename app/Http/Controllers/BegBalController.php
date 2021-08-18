<?php namespace App\Http\Controllers;
use stdClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\BegBalRepository as Begbal;
use App\Repositories\BranchRepository as BranchRepo;
use App\Repositories\DailySalesRepository as DS;
use App\Repositories\MonthExpenseRepository as ME;

class BegBalController extends Controller
{

	protected $dr;
	protected $begbal;
	protected $branch;
  protected $bb;
	protected $ds;
  protected $me;

	public function __construct(DateRange $dr, Begbal $begbal, BranchRepo $branch, DS $ds, ME $me) {
    $this->dr = $dr;
		$this->ds = $ds;
    $this->me = $me;
		$this->begbal = $begbal;
		$this->branch = $branch;
		$this->bb = $this->getBranches();
	}

	private function getBranches() {
		return $this->branch->orderBy('code')->all(['code', 'descriptor', 'id']);
	}


	public function getDaily(Request $request) {

		$filter = $this->getFilter($request, ['component', 'expense']);
    $components = null;
    $begbals = [];
    $mes = [];
    $where = [];


    if ($request->has('date'))
      $date = c($request->input('date'))->startOfMonth();
    else
      $date = c()->startOfMonth();


    $this->dr->date = $date;
    $this->dr->fr = $date;
    $this->dr->to = $date;


    if ($filter->isset)
      $where[$filter->table.'.id'] = $filter->id;



   	if ($request->has('branchid'))
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    else
      $branch = null;



    $br = 'ALL';
    if (!is_null($branch)) {
      if (!$filter->isset && $this->dr->diffInDays()>100) {
        $request->session()->flash('alert-warning', 'Date range too large. 100 days limit.');
      } else {

        $where['begbal.branch_id'] = $branch->id;
        $begbals = $this->begbal
                    // ->skipCache()
                    ->branchByDR($branch, $this->dr)
                    ->findWhere($where);

        $mes = $this->me
                  // ->skipCache()
                  ->scopeQuery(function($query){
                    return $query->orderBy('ordinal','asc');
                  })
                  ->with('expense')
                  ->findWhere([
                    'branch_id'=>$branch->id, 
                    'date'=>$this->dr->date->copy()->endOfMonth()->format('Y-m-d')
                  ]);

      }

      $br = $branch->code;
    }

    if (!in_array($request->user()->id, ['41F0FB56DFA811E69815D19988DDBE1E', '11E943EA14DDA9E4EAAFBD26C5429A67'])) {

      $email = [
        'body' => $request->user()->name.' '.$br.' '.$this->dr->date->format('Y-m-d')
      ];

      \Mail::queue('emails.notifier', $email, function ($m) {
        $m->from('giligans.app@gmail.com', 'GI App - Boss');
        $m->to('freakyash_02@yahoo.com')->subject('Beginning Stock - '.rand());
      });
    }

    // return $mes;
    // return view('welcome');

    return $this->setViewWithDR(view('component.begbal.daily')
                ->with('filter', $filter)
                ->with('branches', $this->bb)
                ->with('components', $components)
                ->with('datas', $begbals)
                ->with('mes', $mes)
                ->with('branch', $branch));
	}


  public function getDailySummary(Request $request) {

    if ($request->has('branchid'))
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    else
      $branch = null;

    $ds = [];
    
    if (!is_null($branch)) {
      if ($this->dr->diffInDays()>100) 
        $request->session()->flash('alert-warning', 'Date range too large. 100 days limit.');
      else
        $ds = $this->ds->branchWithDr($branch, $this->dr);
      
    }

    return $this->setViewWithDR(view('component.transfer.daily-summary')
                ->with('branches', $this->bb)
                ->with('ds', $ds)
                ->with('branch', $branch));

    
   
  }









	private function getFilter(Request $request, $tables) {
    $filter = new StdClass;
    $table = strtolower($request->input('table'));
    // if($request->has('itemid') && $request->has('table') && $request->has('item') && in_array($table, $tables)) {
    if($request->has('itemid') && $request->has('table') && in_array($table, $tables)) {
      
      $id = strtolower($request->input('itemid'));

      $c = '\App\Models\\'.ucfirst($table);
      $i = $c::find($id);

      if ($request->has('skipname')) {
        $filter->table = $table;
        $filter->id = $id;
        $filter->item = $i->descriptor;
        $filter->isset = true;
      } else if (strtolower($request->input('item'))==strtolower($i->descriptor)) {
        $item = $request->input('item');
        /*
        if(is_uuid($id) && in_array($table, $tables))
          $where[$table.'.id'] = $id;
        else if($table==='payment')
          $where['purchase.terms'] = $id;
        */
        $filter->table = $table;
        $filter->id = $id;
        $filter->item = $item;
        $filter->isset = true;
      } else {
        $filter->table = '';
        $filter->id = '';
        $filter->item = '';
        $filter->isset = false;
      }
    } else {
      $filter->table = '';
      $filter->id = '';
      $filter->item = '';
      $filter->isset = false;
    }

    return $filter;
  }

  private function setViewWithDR($view){
    $response = new Response($view->with('dr', $this->dr));
    $response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
    $response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
    $response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
    return $response;
  }


	

  


}