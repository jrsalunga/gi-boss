<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\Purchase2Repository as PurchRepo;
use App\Repositories\SalesmtdRepository as SalesRepo;
use App\Repositories\MonthComponentRepository as mComponent;

class SampleReportController extends Controller
{

	protected $dr;
	protected $salesRepo;
	protected $purchRepo;
	protected $mComponent;

	public function __construct(DateRange $dr, SalesRepo $salesRepo, PurchRepo $purchRepo, mComponent $mComponent) {
		$this->dr = $dr;
		$this->salesRepo = $salesRepo;
		$this->purchRepo = $purchRepo;
		$this->mComponent = $mComponent;
	}


	public function getMonth(Request $request) {
		$this->dr->setMode('monthly');
		return $this->setViewWithDR(view('pnl.monthly'));
	}

	private function setViewWithDR($view){
    $response = new Response($view->with('dr', $this->dr));
    $response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
    $response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
    $response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
    return $response;
  }



  public function getMeat(Request $request) {
  	$datas = [];

  	$branches = \App\Models\Boss\Branch::whereStatus('2')->whereIn('type', ['0', '1', '2', '3'])->orderBy('code')->get();
  	$components = \App\Models\Component::whereIn('compcatid', ['3DF8FB71636311E5B83800FF59FBB323'])
  																		->orderBy('descriptor')->get();


  	foreach ($branches as $key => $branch) {
      $datas[$key]['code'] = $branch->code;

      $a = '';
      try {
        $a = $branch->sector->parent->code;
      } catch (\Exception $e) {

      }

  		$datas[$key]['area'] = $a;

  		$mcs = $this->mComponent->skipCache()->scopeQuery(function($query) use ($branch){ 
  												return $query->leftJoin('component', 'component.id', '=', 'month_component.component_id')
  																		->whereIn('component.compcatid', ['3DF8FB71636311E5B83800FF59FBB323'])
  																		->where('month_component.branch_id', $branch->id)
  																		->where('month_component.date', '2018-10-31');
  												})->all();

  		foreach ($components as $c => $component) {
  			$datas[$key]['components'][$c]['component'] = $component->descriptor;
  			
  			$f = $mcs->filter(function ($item) use ($component){
		      return $item->component_id == $component->id
		      	? $item : null;
		    });
		    $b = $f->first();

  			$datas[$key]['components'][$c]['qty'] = is_null($b) ? NULL: $b->qty;
  		}
  	}

  	return view('sample-report.meat')
  							->with('datas', $datas)
  							->with('components', $components);
  }

  


}