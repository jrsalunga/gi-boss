<?php namespace App\Http\Controllers;

use DB;
use StdClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\SalesmtdRepository as SalesmtdRepo;
use App\Repositories\BossBranchRepository as BBRepo;
use App\Repositories\DailySalesRepository as DSRepo;
use App\Repositories\Criterias\BossBranchCriteria;
use App\Repositories\BranchRepository as BranchRepo;
use App\Models\Product;
use App\Models\Prodcat;
use App\Models\Menucat;
use App\Helpers\Locator;
use App\Repositories\Criterias\ActiveBranchCriteria as ActiveBranch;

class SaleController extends Controller { 

	protected $sale;
  protected $dr;
  protected $ds;
  protected $bb;

  public function __construct(SalesmtdRepo $sale, BBRepo $bbrepo, DateRange $dr, DSRepo $ds, BranchRepo $branch) {
    $this->sale = $sale;
    $this->dr = $dr;
    $this->bb = $bbrepo;
    $this->ds = $ds;
    $this->bb->pushCriteria(new BossBranchCriteria);
    $this->branch = $branch;
    $this->ab = $this->getAMbranches();
  }

  private function getAMbranches() {

    $bb = $this->bb
      ->skipCache()
      ->with([
        'branch'=>function($q){
          $q->select(['code', 'descriptor', 'mancost', 'id']);
        }
      ])->all();

    return collect($bb->pluck('branch')->sortBy('code')->values()->all());
  }

  private function bossBranch(){
    return array_sort($this->branch->active()->all(['code', 'descriptor', 'id']), 
      function ($value) {
        return $value['code'];
    });
  }


  public function getDaily(Request $request) {

    $this->dr->setDateRangeMode($request, 'daily');
    
    $day1 = c('2017-01-01');
    
    //if($day1->)


    $where = [];
    $fields = ['menucat', 'prodcat', 'product'];
    
    $filter = new StdClass;
    if($request->has('itemid') && $request->has('table') && $request->has('item')) {
      
      $id = strtolower($request->input('itemid'));
      $table = strtolower($request->input('table'));

      $c = '\App\Models\\'.ucfirst($table);
      $i = $c::find($id);

      if (strtolower($request->input('item'))==strtolower($i->descriptor)) {
        $item = $request->input('item');
      
        if(is_uuid($id) && in_array($table, $fields))
          $where[$table.'.id'] = $id;
        else if($table==='payment')
          $where['purchase.terms'] = $id;

        $filter->table = $table;
        $filter->id = $id;
        $filter->item = $item;
      } else {
        $filter->table = '';
        $filter->id = '';
        $filter->item = '';
      }

      //$sales = $this->sale->byDateRange($this->dr)->findWhere($where);

    } else {
      $filter->table = '';
      $filter->id = '';
      $filter->item = '';
      

      
    }


    $bb = $this->bossBranch();

    if(is_null($request->input('branchid'))) {
      return $this->setDailyViewVars('product.sales.daily', null, $bb, $filter);
    } 

    if(!is_uuid($request->input('branchid'))
    || !in_array(strtoupper($request->input('branchid')), $this->branch->all()->pluck('id')->all())) 
    {
      return redirect('/product/sales')->with('alert-warning', 'Please select a branch.');
    } 

    try {
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    } catch (Exception $e) {
      return $this->setDailyViewVars('product.sales.daily', null, $bb, $filter);
    }

    $where['salesmtd.branch_id'] = $branch->id;

    $ds = $this->ds
          ->skipCache()
          ->sumByDateRange($this->dr->fr->format('Y-m-d'), $this->dr->to->format('Y-m-d'))
          ->findWhere(['branchid'=>$branch->id])->all();

    $customers = null;
    $backups = null;
    $sales = null;
    if ($this->dr->fr->eq($this->dr->to)) {
        $sales = $this->sale->skipCache()->byDateRange($this->dr)->findWhere($where);
        $customers = $this->getCustomers($sales, $branch->id);
    }
    $backups = $this->checkBackups($this->dr, $branch->code);

    $groupies = $this->aggregateGroupies($this->sale->brGroupies($this->dr)->findWhere($where));

    $menucatid = (app()->environment()==='production') 
      ? 'E83A9DAEBC3711E6856EC3CDBB4216A7'
      : 'E83A9DAEBC3711E6856EC3CDBB4216A7'; // 614D4411BDF211E6978200FF18C615EC
    $mps = $this->aggregateMPs($this->sale->skipCache()->menucatByDR($this->dr, $menucatid)->findWhere($where));
    
    $products = $this->sale
          ->skipCache()
          ->brProductByDR($this->dr)
          ->findWhere($where);

    $prodcats = $this->sale
          //->skipCache()
          ->brProdcatByDR($this->dr)
          ->findWhere($where);

    $menucats = $this->sale
          //->skipCache()
          ->brMenucatByDR($this->dr)
          ->findWhere($where);

  	return $this->setDailyViewVars('product.sales.daily', $branch, $bb, $filter, $sales, $ds[0], $products, $prodcats, $menucats, $groupies, $mps, $backups, $customers);
  }


  private function checkBackups($dr, $brcode) {

    $backups = [];
    $locator = new Locator('backup');
    /* remove 2017-09-21 boss dont see no backups
    foreach ($dr->dateInterval() as $key => $date) {
      $path = strtoupper($brcode).DS.$date->format('Y').DS.$date->format('m').DS.'GC'.$date->format('mdy').'.ZIP';
      if (!$locator->exists($path) && c(now())->gt($date) && c('2017-01-01')->lt($date))
        array_push($backups, $date);
    }
    */
    return $backups;
  }

  private function getCustomers($sales, $branchid) {
    $dr = new DateRange(request());
    $customers = [];
    $customers['totcust'] = 0;
    $customers['sales'] = 0;
    $customers['hours'] = [];
    $ds = \App\Models\DailySales::where(['date'=>$dr->fr->format('Y-m-d'), 'branchid'=>$branchid])->first(['opened_at', 'closed_at', 'custcount']);
    //return $ds = $this->ds->findwhere(['date'=>$dr->fr->format('Y-m-d')], ['opened_at', 'closed_at', 'custcount']);

    if (is_null($ds) && count($ds)>0)
      return null;
    
    if (!isset($ds->opened_at) || !isset($ds->closed_at))
      return null;

    $dr->fr = $ds->opened_at;
    $dr->to = $ds->closed_at;
    
    if ($dr->fr->gte($dr->to))
     return null;

    foreach ($dr->hourInterval() as $k => $date) {
      foreach ($sales as $key => $sale) {
        if ($sale->ordtime->format('H')==$date->format('H')) {
          if (!array_key_exists($date->format('H'), $customers['hours'])) {
            $customers['hours'][$date->format('H')]['date'] = $date; 
            $customers['hours'][$date->format('H')]['custcount'] = $sale->custcount; 
            $customers['hours'][$date->format('H')]['sales'] = $sale->grsamt; 
          } else {
            $customers['hours'][$date->format('H')]['custcount'] += $sale->custcount; 
            $customers['hours'][$date->format('H')]['sales'] += $sale->grsamt; 
          }
          $customers['totcust'] += $sale->custcount;
          $customers['sales'] += $sale->grsamt;
          #unset($sales[$key]);
        }
      }
    }
    return $customers;
  }

  private function aggregateGroupies($grps) {
    $arr = [];

    foreach ($grps as $key => $value) {
      if(array_key_exists($value['group'], $arr)) {
        $arr[$value['group']]['qty']    += $value['qty'];
        $arr[$value['group']]['grsamt'] += $value['grsamt'];
      } else {
        $arr[$value['group']]['group']  = $value['group'];
        $arr[$value['group']]['qty']    = $value['qty'];
        $arr[$value['group']]['grsamt'] = $value['grsamt'];
      }
    }

    return $arr;
  }

  private function aggregateMPs($mps) {
    $arr['ordered'] = [];
    $arr['cancelled'] = [];

    foreach ($mps as $key => $value) {
        
      if(array_key_exists($value['productcode'],  $arr['ordered'])) {
        $arr['ordered'][$value['productcode']]['qty']     += $value['qty'];
        $arr['ordered'][$value['productcode']]['grsamt']  += $value['grsamt'];
      } else {
        $arr['ordered'][$value['productcode']]['productcode'] = $value['productcode'];
        $arr['ordered'][$value['productcode']]['product']     = $value['product'];
        $arr['ordered'][$value['productcode']]['qty']         = $value['qty'];
        $arr['ordered'][$value['productcode']]['grsamt']      = $value['grsamt'];
      }
      
      if ($value['grsamt'] > 0 && $value['qty'] > 0) {
        continue;
      } else {
        array_push($arr['cancelled'], $value);
      }
    }

    return $arr;
  }



  private function setDailyViewVars($view, $branch=null, $branches=null, $filter=null, $sales=null, $ds=null, $products=null, $prodcats=null, $menucats=null, $groupies=null, $mps=null, $backups=null, $customers=null) {

    return $this->setViewWithDR(view($view)
                ->with('branch', $branch)
                ->with('branches', $branches)
                ->with('filter', $filter)
                ->with('sales', $sales)
                ->with('ds', $ds)
                ->with('products', $products)
                ->with('prodcats', $prodcats)
                ->with('groupies', $groupies)
                ->with('mps', $mps)
                ->with('backups', $backups)
                ->with('customers', $customers)
                ->with('menucats', $menucats));
  }



  private function setViewWithDR($view){
    $response = new Response($view->with('dr', $this->dr));
    $response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
    $response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
    $response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
    return $response;
  }




  public function ajaxSales(Request $request, $id) {

    if ($request->ajax()) {
      $data = $this->modalSalesData($request, $id);
      if (!$data)
        return 'Branch not found!';

      return response()->view('analytics.modal.mdl-sales', compact('data'))
                  ->header('Content-Type', 'text/html');
    }
    return abort('404');
  }

  private function modalSalesData(Request $request, $id) {

    $this->dr->setDateRangeMode($request, 'daily');

    try {
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    } catch (Exception $e) {
      return false;
    }

    $where['salesmtd.branch_id'] = $branch->id;

    $ds = $this->ds
    ->skipCache()
    ->find($id);

    $sales = $this->sale
          ->skipCache()
          ->byDateRange($this->dr)
          ->orderBy('ordtime')
          ->findWhere($where);

    $products = $this->sale
          ->skipCache()
          ->brProductByDR($this->dr)
          ->findWhere($where);

    $prodcats = $this->sale
          ->skipCache()
          ->brProdcatByDR($this->dr)
          ->findWhere($where);

    $menucats = $this->sale
          ->skipCache()
          ->brMenucatByDR($this->dr)
          ->findWhere($where);

    $groupies = $this->aggregateGroupies($this->sale->brGroupies($this->dr)->findWhere($where));

    $menucatid = (app()->environment()==='production') 
      ? 'E83A9DAEBC3711E6856EC3CDBB4216A7'
      : 'E83A9DAEBC3711E6856EC3CDBB4216A7'; //614D4411BDF211E6978200FF18C615EC
    $mps = $this->aggregateMPs($this->sale->skipCache()->menucatByDR($this->dr, $menucatid)->findWhere($where));

    $data = [
      'ds' => $ds,
      'sales' => $sales,
      'products' => $products,
      'prodcats' => $prodcats,
      'groupies' => $groupies,
      'mps' => $mps,
      'menucats' => $menucats
    ];

    return $data;
  }





  public function search(Request $request) {


    $arr = [];

    if($request->has('q')) {
      
      $q = $request->input('q');
      $branchid = $request->input('branchid');
      
      $products = Product::where('prodcat_id', '<>', 'E841F22BBC3711E6856EC3CDBB4216A7')
                         ->where(function ($query) use ($q) {
                            $query->where('code', 'like', '%'.$q.'%')
                                  ->orWhere('descriptor', 'like', '%'.$q.'%');
                        })
                        ->orderBy('descriptor')
                        ->get(['code', 'descriptor', 'ucost', 'uprice', 'id']);


      foreach ($products as $product) {
        array_push($arr, ['table'=>'product', 'item'=>$product->descriptor, 'id'=>strtolower($product->id), 'ucost'=> $product->ucost, 'uprice'=>$product->uprice, 'code'=>$product->code]);
      }

      $prodcats = Prodcat::where('descriptor', 'like', '%'.$q.'%')->orderBy('descriptor')->get(['descriptor', 'id']);
      foreach ($prodcats as $prodcat) {
        array_push($arr, ['table'=>'prodcat', 'item'=>$prodcat->descriptor, 'id'=>strtolower($prodcat->id)]);
      }

      $menucats = Menucat::where('descriptor', 'like', '%'.$q.'%')->orderBy('descriptor')->get(['descriptor', 'id']);
      foreach ($menucats as $menucat) {
        array_push($arr, ['table'=>'menucat', 'item'=>$menucat->descriptor, 'id'=>strtolower($menucat->id)]);
      }


      $groupies = config('giligans.groupies');

      $s = preg_grep("/^".strtoupper($q)."/", $groupies);
      if($s) {
        $k = key($s);
        array_push($arr, ['table'=>'groupies', 'item'=>ucwords($s[$k]), 'id'=>strtolower($k)]);
      }
    }

    //return $arr;

    if($request->ajax())
      return $arr;
    else
      return abort('404');

   }


   private function getFilter(Request $request, $tables) {
    $filter = new StdClass;
    $table = strtolower($request->input('table'));
    if($request->has('itemid') && $request->has('table') && $request->has('item') && in_array($table, $tables)) {
      
      $id = strtolower($request->input('itemid'));

      $c = '\App\Models\\'.ucfirst($table);
      $i = $c::find($id);

     // if (strtolower($request->input('item'))==strtolower($i->descriptor)) {
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
    /*  } else {
        $filter->table = '';
        $filter->id = '';
        $filter->item = '';
        $filter->isset = false;
      } */
    } elseif ($table==='groupies') {
      $filter->table = 'groupies';
      $filter->id = strtolower($request->input('itemid'));
      $filter->item = strtoupper($request->input('item'));
      $filter->isset = true;
    } else {
      $filter->table = '';
      $filter->id = '';
      $filter->item = '';
      $filter->isset = false;
    }

    return $filter;
  }


  public function productComparative(Request $request) {
                                      
    //return $this->sale->skipCache()->groupiesSalesByDR($this->dr, '0c17fe2d78a711e587fa00ff59fbb323', 'F1');                                 

    $filter = $this->getFilter($request, ['menucat', 'prodcat', 'product']);
    
    $datas = [];
    $graphs = [];
    $branches = $this->branch
                    //->skipCache()
                    ->orderBy('code')
                    ->getByCriteria(new ActiveBranch)
                    ->all(['code', 'descriptor', 'id']);



    if ($filter->isset) {


      foreach ($branches as $key => $branch) {
        
        switch ($filter->table) {
          case 'product':
            $datas[$branch->code] = $this->sale
                                        //->skipCache()
                                        ->productSalesByDR($this->dr)
                                        ->findwhere([
                                          'salesmtd.product_id'=> $filter->id,
                                          'salesmtd.branch_id' => $branch->id
                                        ])->first();
            break;
          case 'prodcat':
            $datas[$branch->code] = $this->sale
                                        //->skipCache()
                                        ->prodcatSalesByDR($this->dr)
                                        ->findwhere([
                                          'product.prodcat_id'=> $filter->id,
                                          'salesmtd.branch_id' => $branch->id
                                        ])->first();  
            break;
          case 'menucat':
            $datas[$branch->code] = $this->sale
                                        //->skipCache()
                                        ->menucatSalesByDR($this->dr)
                                        ->findwhere([
                                          'product.menucat_id'=> $filter->id,
                                          'salesmtd.branch_id' => $branch->id
                                        ])->first();
            break;
          case 'groupies':
            $datas[$branch->code] = $this->sale
                                            ->groupiesSalesByDR($this->dr, $branch->id, strtoupper($filter->id));
            break;
          default:
            $datas[$branch->code] = NULL;
            break;
        }
        
        
        
        if (in_array($branch->code, $this->ab->pluck('code')->toArray())) {
          $k = array_search($branch->code, $this->ab->pluck('code')->toArray());
          $graphs[$branch->code] = $datas[$branch->code];
        }
      }
      //return $datas;
      
    }


    
    return $this->setViewWithDR(view('product.sales.comparative')
              ->with('filter', $filter)
              ->with('branches', $branches)
              ->with('graphs', $graphs)
              ->with('datas', $datas));
  }

  private function getByProduct($filter, $branch) {
      
    $products = $this->sale
                    //->skipCache()
                    ->productSalesByDR($this->dr)
                    ->findwhere([
                      'salesmtd.product_id'=> $filter->id,
                      'salesmtd.branch_id' => $branch->id
                    ]);

    if(count($products)>0) 
      return $products->first();
    else
      return NULL;
  }







}
