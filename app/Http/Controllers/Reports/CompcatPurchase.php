<?php namespace App\Http\Controllers\Reports;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\Purchase2Repository as PurchRepo;
use App\Repositories\SalesmtdRepository as SalesRepo;
use App\Repositories\MonthComponentRepository as mComponent;
use App\Repositories\BranchRepository as BranchRepo;
use App\Repositories\CompcatRepository as CompcatRepo;

class CompcatPurchase extends Controller
{

	protected $dr;
	protected $salesRepo;
	protected $purchRepo;
  protected $mComponent;
  protected $branch;
  protected $compcat;
	protected $bb;

	public function __construct(DateRange $dr, BranchRepo $branch, SalesRepo $salesRepo, PurchRepo $purchRepo, mComponent $mComponent, CompcatRepo $compcat) {
		$this->dr = $dr;
		$this->salesRepo = $salesRepo;
		$this->purchRepo = $purchRepo;
    $this->mComponent = $mComponent;
    $this->branch = $branch;
		$this->compcat = $compcat;
	  $this->bb = $this->getBranches();
  }

  private function getBranches() {
    return $this->branch->orderBy('code')->all(['code', 'descriptor', 'id']);
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



  public function getCompcatPurchase(Request $request) {
    $date = carbonCheckorNow($request->input('date'));
    $this->dr->date = $date = $date->copy()->endOfMonth();
    $this->dr->fr = $date->copy()->startOfMonth();
    $this->dr->to = $date->copy()->endOfMonth();

    $datas = [];

    if ($request->has('branchid') && is_uuid($request->input('branchid')))
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    else
      $branch = null;

    if ($request->has('compcatid') && is_uuid($request->input('compcatid')))
      $compcat = $this->compcat->find(strtolower($request->input('compcatid')));
    else
      $compcat = null;

    if (!is_null($compcat))
      $datas = $this->getCompcatPurchasedata($date, $branch, $compcat);
    else
      $datas['components'] = $datas['datas'] = NULL;

    //return dd($datas['datas']);
    return $this->setViewWithDR(view('report.compcat-purchase')
                ->with('compcats', $this->compcat->orderBy('descriptor')->findWhere(['valid'=>'1']))
                ->with('branches', $this->bb)
                ->with('datas', $datas['datas'])
                ->with('components', $datas['components'])
                ->with('compcat', $compcat)
                ->with('branch', $branch));
  }
  
  public function getCompcatPurchasedata($date, $branch, $compcat) {
  	$datas = [];
    $datas['datas'] = NULL;
    $datas['components'] = NULL;

    $branches = \App\Models\Boss\Branch::whereStatus('2')->whereIn('type', ['0', '1', '2', '3'])->orderBy('code')->get();
    $components = \App\Models\Component::where('compcatid', [$compcat->id])
                                      ->orderBy('descriptor')->get();


    foreach ($branches as $key => $branch) {
      $datas[$key]['code'] = $branch->code;

      $a = '';
      try {
        $a = $branch->sector->parent->code;
      } catch (\Exception $e) {

      }

      $datas[$key]['area'] = $a;

      $mcs = $this->mComponent->skipCache()->scopeQuery(function($query) use ($branch, $compcat, $date){ 
                          return $query->leftJoin('component', 'component.id', '=', 'month_component.component_id')
                                      ->whereIn('component.compcatid', [$compcat->id])
                                      ->where('month_component.branch_id', $branch->id)
                                      ->where('month_component.date', $date->format('Y-m-d'));
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
    
    $datas['datas'] = $datas;
    $datas['components'] = $components;
    
    return $datas;

    return view('report.compcat-purchase')
                ->with('datas', $datas)
                ->with('components', $components);
  }

  


}