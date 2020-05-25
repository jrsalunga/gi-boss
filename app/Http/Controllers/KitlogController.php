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

  private function toDatatables($array) {
    $datas = [];

    foreach($array as $i => $f) {

      $null_ctr = 0;
      if (is_null($f->product->menucat)) {
        $datas[99]['menucat'] = 'UNKNOWN';
        $datas[99]['seqno'] = 99;
        $datas[99]['items'] = [];
        if ($null_ctr==0) {
          $datas[99]['line'] = 1;
          $datas[99]['qty'] = $f->qty;
        } else {
          $datas[99]['line']++;
          $datas[99]['qty'] += $f->qty;
        }
        array_push($datas[99]['items'], $f);
        $null_ctr++;
      } else {
        $mid = $f->product->menucat->id;
        $k = $f->product->menucat->seqno;          
        if(array_key_exists($k, $datas)) {
          array_push($datas[$k]['items'], $f);
          $datas[$k]['line']++;
          $datas[$k]['qty'] += $f->qty;
        } else {
          $datas[$k]['menucat'] = $f->product->menucat->descriptor;
          $datas[$k]['line'] = 1;
          $datas[$k]['qty'] = $f->qty;
          $datas[$k]['seqno'] = $k;
          $datas[$k]['items'] = [];
          array_push($datas[$k]['items'], $f);
        }
      }
    }

    ksort($datas);
    return $datas;
  }

  public function toArea($array) {
    $datas = [];

    $areas = ['C'=>'Center', 'D'=>'Dispatching', 'F'=>'Frying', 'G'=>'Grilling', 'X'=>'Not Set'];

    foreach($areas as $k => $area) {
      $datas[$k]['area'] = $area;
      $datas[$k]['qty'] = 0;
      $datas[$k]['line'] = 0;
      $datas[$k]['items'] = [];
      $datas[$k]['status'] = NULL;
    }

    foreach($array as $i => $f) {
      if (array_key_exists($f->area, $areas)) {
        if (is_null($datas[$f->area]['status'])) {
          $datas[$f->area]['area'] = $areas[$f->area];
          $datas[$f->area]['status'] = 1;
        }
        $datas[$f->area]['qty'] += $f->qty;
        $datas[$f->area]['line']++;
        array_push($datas[$f->area]['items'], $f);
      } else {
        if (is_null($datas['X']['status'])) {
          $datas['X']['area'] = $areas['X'];
          $datas['X']['status'] = 1;
        }
        $datas['X']['qty'] += $f->qty;
        $datas['X']['line']++;
        array_push($datas['X']['items'], $f);
      }     
    }
    return $datas;
  }

  public function getMonth(Request $request) {

    $bb = $this->branch->active()->all(['code', 'descriptor', 'id']);
    $d = carbonCheckorNow($request->input('date'));
    $date = $d->copy()->endOfMonth();

    $dts = [];
    if(!$request->has('branchid') && !isset($_GET['branchid'])) 
    {    
      $areas = $this->datasetArea->orderBy('area')->findWhere(['date'=>$date->format('Y-m-d'), 'branch_id'=>'all']);
      $foods = $this->datasetFood->with(['product.menucat'])->findWhere(['date'=>$date->format('Y-m-d'), 'branch_id'=>'all']);
      
      return view('kitlog.month-menucat')
                ->with('branches', $bb)
                ->with('branch', NULL)
                ->with('date', $date)
                ->with('areas', $areas)
                ->with('foods', $foods)
                ->with('dtareas', $this->toArea($foods))
                ->with('datatables', $this->toDatatables($foods));

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
      $foods = $this->datasetFood->skipCache()->with(['product.menucat'])->findWhere(['date'=>$date->format('Y-m-d'), 'branch_id'=>'all']);

      // return $this->toDatatables($foods);
      // return $this->toArea($foods);

      return view('kitlog.month-menucat')
                ->with('branches', $bb)
                ->with('branch', NULL)
                ->with('date', $date)
                ->with('areas', $areas)
                ->with('foods', $foods)
                ->with('dtareas', $this->toArea($foods))
                ->with('datatables', $this->toDatatables($foods));

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
    $foods = $this->datasetFood->with(['product.menucat'])->findWhere(['date'=>$date->format('Y-m-d'), 'branch_id'=>$branch->id]);

    return view('kitlog.month-menucat')
                ->with('branches', $bb)
                ->with('branch', $branch)
                ->with('date', $date)
                ->with('areas', $areas)
                ->with('foods', $foods)
                ->with('dtareas', $this->toArea($foods))
                ->with('datatables', $this->toDatatables($foods));

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
    $datatables = [];
    if (count($where)>0) {
      $relation = is_null($branch) ? ['product', 'menucat', 'branch'] : ['product', 'menucat'];
      try {
        $kitlogs = $this->kitlog
                  ->skipCache()
                  ->with($relation)
                  ->scopeQuery(function($query){
                    return $query->whereBetween('date', [$this->dr->fr->format('Y-m-d'), $this->dr->to->format('Y-m-d')]);
                  })
                  ->orderBy('date')
                  ->orderBy('ordtime')
                  ->findWhere($where);
      } catch (\Exception $e) {
        throw new \Exception("Error Processing Request: ".$e->getMessage(), 1);
        
      }

      foreach($kitlogs as $kl) {
        if(array_key_exists($kl->menucat_id, $datatables)) {
          array_push($datatables[$kl->menucat_id]['items'], $kl);
        } else {
          $datatables[$kl->menucat_id]['items'] = [];
          $datatables[$kl->menucat_id]['menucat'] = is_null($kl->menucat) ? $kl->menucat_id : $kl->menucat->descriptor;
          array_push($datatables[$kl->menucat_id]['items'], $kl);
        }
      }
    }

    // return $datatables;

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
            ->whereNotIn('menucat_id', ['A197E8FFBC7F11E6856EC3CDBB4216A7', '24F15101E45111E69815D19988DDBE1E', 'E839E5BCBC3711E6856EC3CDBB4216A7', 'E84204C8BC3711E6856EC3CDBB4216A7'])
            ->where('code', '<>', 'MISC')
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