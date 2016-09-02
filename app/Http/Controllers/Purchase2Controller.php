<?php namespace App\Http\Controllers;

use StdClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\Purchase2Repository as PurchaseRepo;
use App\Http\Requests\GetComponentPurchasedRequest;
use App\Repositories\BossBranchRepository as BBRepo;
use App\Repositories\Criterias\BossBranchCriteria;
use App\Repositories\BranchRepository;
use App\Models\Component;
use App\Models\Compcat;
use App\Models\Expense;
use App\Models\Expscat;
use App\Models\Supplier;


class Purchase2Controller extends Controller { 

	protected $purchased;
  protected $dr;
  protected $bb;

  public function __construct(PurchaseRepo $purchased, BBRepo $bbrepo, DateRange $dr) {
    $this->purchased = $purchased;
    $this->dr = $dr;
    $this->bb = $bbrepo;
    $this->bb->pushCriteria(new BossBranchCriteria);
    $this->branch = new BranchRepository;
  }

  private function bossBranch(){
    return array_sort($this->branch->active()->all(['code', 'descriptor', 'id']), 
      function ($value) {
        return $value['code'];
    });
  }


  private function setDailyViewVars($view, $purchases=null, $branches=null, $branch=null, $filter=null) {

    return $this->setViewWithDR(view($view)
                ->with('purchases', $purchases)
                ->with('branches', $branches)
                ->with('branch', $branch)
                ->with('filter', $filter));
  }



	public function getDaily(Request $request) {

		$where = [];
		$fields = ['component', 'supplier', 'expense', 'expscat', 'compcat'];
		
		$filter = new StdClass;
		if($request->has('itemid') && $request->has('table')){
			
			$id = strtolower($request->input('itemid'));
			$table = strtolower($request->input('table'));
			$item = $request->input('item');
			
			if(is_uuid($id) && in_array($table, $fields))
				$where[$table.'.id'] = $id;

			$filter->table = $table;
			$filter->id = $id;
			$filter->item = $item;
		} else {
			$filter->table = '';
			$filter->id = '';
			$filter->item = '';
		}

		

		$bb = $this->bossBranch();
    $res = $this->setDateRangeMode($request, 'daily');

    //if(!$request->has('branchid')) {
    if(is_null($request->input('branchid'))) {
      return $this->setDailyViewVars('component.purchased.daily', null, $bb, null, $filter);
    } 

    if(!is_uuid($request->input('branchid'))
    || !in_array(strtoupper($request->input('branchid')), $this->branch->all()->pluck('id')->all())) 
    {
      return redirect('/component/purchases')->with('alert-warning', 'Please select a branch.');
    } 

    try {
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    } catch (Exception $e) {
      return $this->setDailyViewVars('component.purchased.daily', null, $bb, null, $filter);
    }

		$where['purchase.branchid'] = $branch->id;

    $purchases = $this->purchased
    								->branchByDR($branch, $this->dr)
    								->findWhere($where);

    return $this->setDailyViewVars('component.purchased.daily', $purchases, $bb, $branch, $filter);
	
	}

































	// modify the date on DateRange instanced based on the 'mode'
  private function setDateRangeMode(Request $request, $mode='day') { 
    $y=false;
    switch ($mode) {
      case 'month':
        $to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->endOfMonth();
        $fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $to->copy()->subMonths(5)->startOfMonth();
        if ($to->lt($fr)) {
          $to = Carbon::now()->endOfMonth();
          $fr = $to->copy()->subMonths(5)->startOfMonth(); //$to->copy()->startOfMonth();
        } else {
          $to = $to->endOfMonth();
          $fr = $fr->startOfMonth();
        }
        break;
      case 'daily':
        $to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->endOfMonth();
        $fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $to->copy()->startOfMonth();
        if ($to->lt($fr)) {
          $to = Carbon::now();
          $fr = $to->copy()->startOfMonth();
        }
        break;
      case 'weekly':
        $to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->endOfWeek();
        $fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $to->copy()->subWeeks(5)->startOfWeek();
        if ($to->lt($fr)) {
          $to = Carbon::now()->endOfWeek();
          $fr = $to->copy()->subWeeks(5)->startOfWeek(); //$to->copy()->startOfWeek();
        } else {
          $to = $to->endOfWeek();
          $fr = $fr->startOfWeek();
        }
        break;
      case 'quarterly':
        $to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->lastOfQuarter();
        $fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $to->copy()->subMonths(11)->firstOfQuarter();
        if ($to->lt($fr)) {
          $to = Carbon::now()->lastOfQuarter();
          $fr = $to->copy()->subMonths(12)->firstOfQuarter(); //$to->copy()->startOfWeek();
        } else {
          $to = $to->lastOfQuarter();
          $fr = $fr->firstOfQuarter();
        }
        break;
      case 'yearly':
        $y=true;
        $to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->lastOfYear();
        $fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $to->copy()->subYear()->firstOfYear();
        if ($to->lt($fr)) {
          $to = Carbon::now()->lastOfYear();
          $fr = $to->copy()->subYear()->firstOfYear(); //$to->copy()->startOfWeek();
        } else {
          $to = $to->lastOfYear();
          $fr = $fr->firstOfYear();
        }
        break;
      default:
        $to = Carbon::now()->endOfMonth();
        $fr = $to->copy()->startOfMonth();
        break;
    }
    

    if(!$y){
      
      // if more than a year
      if($fr->diffInDays($to, false)>=731) { // 730 = 2yrs
        $this->dr->fr = $to->copy()->subDays(730)->startOfMonth();
        $this->dr->to = $to;
        $this->dr->date = $to;
        return false;
      }
    }


    $this->dr->fr = $fr;
    $this->dr->to = $to;
    $this->dr->date = $to;
    return true;
  }

  private function setViewWithDR($view){
    $response = new Response($view->with('dr', $this->dr));
    $response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
    $response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
    $response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
    return $response;
  }


  public function search(Request $request) {


  	$arr = [];

	  if($request->has('q')) {
	  	
	  	$q = $request->input('q');
	  	
	  	$components = Component::where('descriptor', 'like', '%'.$q.'%')->get(['descriptor', 'id']);
	  	foreach ($components as $component) {
	  		array_push($arr, ['table'=>'component', 'item'=>$component->descriptor, 'id'=>strtolower($component->id)]);
	  	}

	  	$compcats = Compcat::where('descriptor', 'like', '%'.$q.'%')->get(['descriptor', 'id']);
	  	foreach ($compcats as $compcat) {
	  		array_push($arr, ['table'=>'compcat', 'item'=>$compcat->descriptor, 'id'=>strtolower($compcat->id)]);
	  	}

	  	$expenses = Expense::where('descriptor', 'like', '%'.$q.'%')->get(['descriptor', 'id']);
	  	foreach ($expenses as $expense) {
	  		array_push($arr, ['table'=>'expense', 'item'=>$expense->descriptor, 'id'=>strtolower($expense->id)]);
	  	}

	  	$expscats = Expscat::where('descriptor', 'like', '%'.$q.'%')->get(['descriptor', 'id']);
	  	foreach ($expscats as $expscat) {
	  		array_push($arr, ['table'=>'expscat', 'item'=>$expscat->descriptor, 'id'=>strtolower($expscat->id)]);
	  	}

	  	$suppliers = Supplier::where('descriptor', 'like', '%'.$q.'%')->get(['descriptor', 'id']);
	  	foreach ($suppliers as $supplier) {
	  		array_push($arr, ['table'=>'supplier', 'item'=>$supplier->descriptor, 'id'=>strtolower($supplier->id)]);
	  	}


	  }


	  return $arr;


	  	if($request->ajax())
	  		return 'ajax';
	  	else
	  		return abort('404');

	 }


}