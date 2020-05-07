<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\KitlogRepository as KitlogRepo;
use App\Repositories\Kitlog\DayAreaRepository as DayArea;
use App\Repositories\Kitlog\DayFoodRepository as DayFood;
use App\Repositories\Kitlog\MonthAreaRepository as MonthArea;
use App\Repositories\Kitlog\DayFoodRepository as MonthFood;
use App\Repositories\Kitlog\DatasetAreaRepository as DatasetArea;
use App\Repositories\Kitlog\DatasetFoodRepository as DatasetFood;
use App\Repositories\Boss\BranchRepository as Branch;
use App\Repositories\ProductRepository as Product;

class KitlogController extends Controller {

  protected $dr;
  private $kitlog;
  private $dayArea;
  private $dayFood;
  private $monthArea;
  private $monthFood;
  private $datasetArea;
  private $datasetFood;
  private $branch;
  private $product;

  public function __construct(DateRange $dr, KitlogRepo $kitlog, DayArea $dayArea, DayFood $dayFood, MonthArea $monthArea, MonthFood $monthFood, DatasetArea $datasetArea, DatasetFood $datasetFood, Branch $branch, Product $product) {
    $this->dr = $dr;
    $this->kitlog = $kitlog;
    $this->dayArea = $dayArea;
    $this->dayFood = $dayFood;
    $this->monthArea = $monthArea;
    $this->monthFood = $monthFood;
    $this->datasetArea = $datasetArea;
    $this->datasetFood = $datasetFood;
    $this->branch = $branch;
    $this->product = $product;
  }

  public function index(Request $request) {

    return view('kitlog.index');
  }

  public function getMonth(Request $request) {

    $bb = $this->branch->active()->all(['code', 'descriptor', 'id']);
    $d = carbonCheckorNow($request->input('date'));
    $date = $d->copy()->endOfMonth();


    if(!$request->has('branchid') && !isset($_GET['branchid'])) 
    {    
      $areas = $this->datasetArea->orderBy('area')->findWhere(['date'=>$date->format('Y-m-d'), 'branch_id'=>'all']);
      $foods = $this->datasetFood->with(['product'])->findWhere(['date'=>$date->format('Y-m-d'), 'branch_id'=>'all']);
      return view('kitlog.month')
                ->with('branches', $bb)
                ->with('branch', NULL)
                ->with('date', $date)
                ->with('areas', $areas)
                ->with('foods', $foods);
    } 

    if(!is_uuid($request->input('branchid'))
    || !in_array(strtoupper($request->input('branchid')), collect($bb)->pluck('id')->all())) 
    {
      $areas = $this->datasetArea->orderBy('area')->findWhere(['date'=>$date->format('Y-m-d'), 'branch_id'=>'all']);
      $foods = $this->datasetFood->with(['product'])->findWhere(['date'=>$date->format('Y-m-d'), 'branch_id'=>'all']);
      return view('kitlog.month')
                ->with('branches', $bb)
                ->with('branch', NULL)
                ->with('date', $date)
                ->with('areas', $areas)
                ->with('foods', $foods);
    } 

    try {
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    } catch (Exception $e) {
      return redirect('/kitlog/month-area')->with('alert-warning', 'Please select a branch.');
    }

    $areas = $this->datasetArea->orderBy('area')->findWhere(['date'=>$date->format('Y-m-d'), 'branch_id'=>$branch->id]);
    $foods = $this->datasetFood->with(['product'])->findWhere(['date'=>$date->format('Y-m-d'), 'branch_id'=>$branch->id]);

    return view('kitlog.month')
                ->with('branches', $bb)
                ->with('branch', $branch)
                ->with('date', $date)
                ->with('areas', $areas)
                ->with('foods', $foods);
  }

  public function getLogs(Request $request) {

    $this->dr->setDateRangeMode($request, 'daily');
    $bb = $this->branch->active()->all(['code', 'descriptor', 'id']);

    $where = [];
    
    $branch = NULL;
    if ($request->has('branchid') && is_uuid($request->input('branchid'))) {
      try {
        $branch = $this->branch->find(strtolower($request->input('branchid')), ['code', 'descriptor', 'id']);
      } catch (Exception $e) {
        throw $e;
      }
      $where['branch_id'] = $request->input('branchid');
    }

    $days_diff = NULL;
    $product = NULL;
    if ($request->has('productid') && is_uuid($request->input('productid'))) {
      try {
        $product = $this->product->find(strtolower($request->input('productid')));
      } catch (Exception $e) {
        throw $e;
      }
      $where['product_id'] = $request->input('productid');
    } else {
      if ($this->dr->diffInDays()>30) {
        $days_diff = $this->dr->diffInDays();
        $this->dr->to = $this->dr->fr->copy()->addDays(30);
      }
    }

    if ($request->has('iscombo') && (in_array($request->has('iscombo'),[0,1])))
      $where['iscombo'] = $request->input('iscombo');

    $kitlogs = [];
    if (count($where)>0) {
      $kitlogs = $this->kitlog
                  //->skipCache()
                  ->with(['product', 'menucat'])
                  ->scopeQuery(function($query){
                    return $query->whereBetween('date', [$this->dr->fr->format('Y-m-d'), $this->dr->to->format('Y-m-d')]);
                  })
                  ->orderBy('date')
                  ->orderBy('ordtime')
                  ->findWhere($where);
    }

   return $this->setViewWithDR(view('kitlog.logs')
                  ->with('days_diff', $days_diff)
                  ->with('kitlogs', $kitlogs)
                  ->with('branches', $bb)
                  ->with('branch', $branch)
                  ->with('product', $product));
  }

  public function getChecklist(Request $request) {

    $bb = $this->branch->active()->all(['code', 'descriptor', 'id']);
    $d = carbonCheckorNow($request->input('date'));
    $date = $d->copy()->endOfMonth();
    $f = $date->copy()->startOfMonth();
    $t = $date;

    $branch = NULL;
    $datas = [];
    if ($request->has('branchid') && is_uuid($request->input('branchid'))) {
      try {
        $branch = $this->branch->find(strtolower($request->input('branchid')), ['code', 'descriptor', 'id']);
      } catch (Exception $e) {
        throw $e;
      }
      $where['branch_id'] = $request->input('branchid');


      // $dss = \App\Models\DailySales::whereBetween('date', [$f->format('Y-m-d'), $t->format('Y-m-d')])
      //                               ->where('branchid', $branch->id)
      //                               ->orderBy('date')
      //                               ->get(['date', 'kitlog', 'change_item', 'change_item_diff', 'id']);


    // return dd($dss);

      foreach(dateInterval($f, $t) as $k => $d) {

        $ds  = \App\Models\DailySales::whereBetween('date', [$d->format('Y-m-d'), $d->format('Y-m-d')])
                                      ->where('branchid', $branch->id)
                                      ->first(['branchid', 'kitlog', 'change_item', 'change_item_diff', 'id']);

        $datas[$k]['date'] = $d;
        $datas[$k]['ds'] = $ds;
      } 
    }
    // return $datas;

    

    return $this->setViewWithDR(view('kitlog.checklist')
                  ->with('date', $date)
                  ->with('datas', $datas)
                  ->with('branches', $bb)
                  ->with('branch', $branch));


  }


  public function searchProduct(Request $request, $param1=null) {

    $limit = empty($request->input('maxRows')) ? 10:$request->input('maxRows'); 
    $res = \App\Models\Product::where(function ($query) use ($request) {
              $query->orWhere('code', 'like', '%'.$request->input('q').'%')
                ->orWhere('descriptor', 'like',  '%'.$request->input('q').'%');
            })
            ->orderBy('descriptor')
            ->take($limit)
            ->get();

    return $res;
  }

  /*
  public function search(Request $request) {

    $arr = [];

    if($request->has('q')) {
      
      $q = $request->input('q');
      
      $products = \App\Models\Product::where('descriptor', 'like', '%'.$q.'%')->orderBy('descriptor')->get(['descriptor', 'id']);
      foreach ($products as $product) {
        array_push($arr, ['table'=>'product', 'item'=>$product->descriptor, 'id'=>strtolower($product->id)]);
      }

      $menucats = \App\Models\Menucat::where('descriptor', 'like', '%'.$q.'%')->orderBy('descriptor')->get(['descriptor', 'id']);
      foreach ($menucats as $menucat) {
        array_push($arr, ['table'=>'menucat', 'item'=>$menucat->descriptor, 'id'=>strtolower($menucat->id)]);
      }
    }

    if($request->ajax())
      return $arr;
    else
      return abort('404');
  }
  */
  
  public function test(Request $request) {
    
    $d = $this->dr->date->copy()->endOfMonth();

    // return $this->kitlog->aggregateAllProductDatasetByDr($this->dr->date->copy()->startOfMonth(), $d);
    $a = $this->kitlog->aggregateAllAreaDatasetByDr($this->dr->date->copy()->startOfMonth(), $d);

    $data = [];

    foreach ($a as $k => $v) {
      if(array_key_exists($v->area, $data)) {
        array_push($data[$v->area]['dataset'], $v->grp.'|'.$v->txn.'|'.$v->qty);
      } else {
        $data[$v->area]['area'] = $v->area;
        $data[$v->area]['date'] = $v->date->format('Y-m-d');
        $data[$v->area]['dataset'] = [$v->grp.'|'.$v->txn.'|'.$v->qty];
      }
    }


    $this->datasetArea->deleteWhere(['date'=>$d->format('Y-m-d')]);
    
    return $as = $this->kitlog->aggregateAllAreaByDr($d->copy()->startOfMonth(), $d->copy()->endOfMonth());


    foreach($data as $key => $area) {
      $res = $this->datasetArea->create([
        'date' => $data[$key]['date'],
        'area' => $data[$key]['area'],
        'dataset' => implode(",", $data[$key]['dataset'])
      ]
      );
    }


    $areas = $this->datasetArea->findWhere(['date'=>$d->format('Y-m-d')]);
    $dataset = [];

    foreach ($areas as $i => $area) {
      $z = explode(',', $area->dataset);
      $dataset[$i] = [];
      foreach($z as $ds) {
        $y = explode('|', $ds);
        array_push($dataset[$i], $y[2]);
      }
    }

    return $dataset;


    return $this->kitlog->aggregateAllProductDatasetByDr($this->dr->date->copy()->startOfMonth(), $this->dr->date->copy()->endOfMonth());
  }

  private function setViewWithDR($view){
    $response = new Response($view->with('dr', $this->dr));
    $response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
    $response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
    $response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
    return $response;
  }
}