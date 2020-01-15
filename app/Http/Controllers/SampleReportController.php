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
  	$components = \App\Models\Component::whereIn('compcatid', ['3DE82C56636311E5B83800FF59FBB323'])
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
  																		->whereIn('component.compcatid', ['3DE82C56636311E5B83800FF59FBB323'])
  																		->where('month_component.branch_id', $branch->id)
  																		->where('month_component.date', '2019-12-31');
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


  public function getProdcat(Request $request) {
    $branch = new \App\Models\Boss\Branch;
    $prodcat = new \App\Models\Prodcat;
    $mprodcat = new \App\Models\MonthProdcat;
    $ds = new \App\Models\MonthlySales;



    $branches = $branch
        ->select('code', 'descriptor', 'id')
        ->where('status', 2)
        ->where('type', '<', 4)
        ->orderBy('code')
        ->get();

    $prodcats = $prodcat
        ->where('ordinal', '>', 0)
        ->orderBy('ordinal')
        ->get();


    $datas = [];

    foreach ($branches as $k => $b) {
      foreach (['2019-06-30', '2019-07-31'] as $l => $d) {
        $dss = $ds->where('branch_id', $b->id)->where('date', $d)->first();
        $date = Carbon::parse($d);
        
        $datas[$b->code][$l]['date'] = $date;

        
        if (is_null($dss)) {
          $datas[$b->code][$l]['sales'] = 0;
          $datas[$b->code][$l]['gross'] = 0;
          $datas[$b->code][$l]['food_sales'] = 0;
          $datas[$b->code][$l]['cos'] = 0;
          $datas[$b->code][$l]['cospct'] = 0;
          $datas[$b->code][$l]['purchcost'] = 0;
          $datas[$b->code][$l]['transcost'] = 0;
          $datas[$b->code][$l]['transcos'] = 0;
          $datas[$b->code][$l]['transncos'] = 0;
          $datas[$b->code][$l]['emp_meal'] = 0;
          $datas[$b->code][$l]['opex'] = 0;
        } else {
          $datas[$b->code][$l]['sales'] = $dss->sales;
          $datas[$b->code][$l]['gross'] = $dss->slsmtd_totgrs;
          $datas[$b->code][$l]['food_sales'] = $dss->food_sales;
          $datas[$b->code][$l]['cos'] = $dss->cos;
          $datas[$b->code][$l]['cospct'] = $dss->fc;
          $datas[$b->code][$l]['purchcost'] = $dss->purchcost;
          $datas[$b->code][$l]['transcost'] = $dss->transcost;
          $datas[$b->code][$l]['transcos'] = $dss->transcos;
          $datas[$b->code][$l]['transncos'] = $dss->transncos;
          $datas[$b->code][$l]['emp_meal'] = $dss->emp_meal;
          $datas[$b->code][$l]['opex'] = $dss->opex;
        }
      }
    }
    
   return view('sample-report.prodcat')
                ->with('datas', $datas);
  }

  


}