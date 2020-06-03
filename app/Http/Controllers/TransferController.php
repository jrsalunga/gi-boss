<?php namespace App\Http\Controllers;
use stdClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\StockTransferRepository as TransferRepo;
use App\Repositories\BranchRepository as BranchRepo;
use App\Repositories\DailySalesRepository as DS;

class TransferController extends Controller
{

	protected $dr;
	protected $transfer;
	protected $branch;
  protected $bb;
	protected $ds;

	public function __construct(DateRange $dr, TransferRepo $transfer, BranchRepo $branch, DS $ds) {
    $this->dr = $dr;
		$this->ds = $ds;
		$this->transfer = $transfer;
		$this->branch = $branch;
		$this->bb = $this->getBranches();
	}

	private function getBranches() {
		return $this->branch->orderBy('code')->all(['code', 'descriptor', 'id']);
	}


	public function getDaily(Request $request) {

		$filter = $this->getFilter($request, ['component', 'expense']);
    $components = null;
    $transfers = [];
    $where = [];

   // return dd($filter);

    if ($filter->isset)
      $where[$filter->table.'.id'] = $filter->id;
      //$where['stocktransfer.'.$filter->table.'id'] = $filter->id;

   	if ($request->has('branchid'))
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    else
      $branch = null;

    
    if (!is_null($branch)) {
      if (!$filter->isset && $this->dr->diffInDays()>100) {
        $request->session()->flash('alert-warning', 'Date range too large. 100 days limit.');
      } else {

        $where['stocktransfer.branchid'] = $branch->id;
        $transfers = $this->transfer
                    //->skipCache()
                    ->branchByDR($branch, $this->dr)
                    ->withRelations()
                    ->findWhere($where);

      }
    }

    return $this->setViewWithDR(view('component.transfer.daily')
                ->with('filter', $filter)
                ->with('branches', $this->bb)
                ->with('components', $components)
                ->with('transfers', $transfers)
                ->with('branch', $branch));


		return $this->bb;
		return view('component.transfer.daily');
		return $this->transfer
								//->skipCache()
								->withRelations()
								->findWhere(['branchid'=> '0C17FE2D78A711E587FA00FF59FBB323', 'date'=>'2017-09-23']);
	}


  public function getDailySummary(Request $request) {

    if ($request->has('branchid'))
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    else
      $branch = null;

    $ds = NULL;
    
    if (!is_null($branch)) {
      if ($this->dr->diffInDays()>100) 
        $request->session()->flash('alert-warning', 'Date range too large. 100 days limit.');
      else {
        $ds = $this->ds->branchWithDr($branch, $this->dr);
      }
      
    }

    return $this->setViewWithDR(view('component.transfer.daily-summary')
                ->with('branches', $this->bb)
                ->with('ds', $ds)
                ->with('branch', $branch));

    
   
  }









	private function getFilter(Request $request, $tables) {
    $filter = new StdClass;
    $table = strtolower($request->input('table'));
    if($request->has('itemid') && $request->has('table') && $request->has('item') && in_array($table, $tables)) {
      
      $id = strtolower($request->input('itemid'));

      $c = '\App\Models\\'.ucfirst($table);
      $i = $c::find($id);

      if (strtolower($request->input('item'))==strtolower($i->descriptor)) {
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