<?php namespace App\Http\Controllers;

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
use App\Repositories\BranchRepository;
use App\Models\Product;
use App\Models\Prodcat;
use App\Models\Menucat;


class SaleController extends Controller { 

	protected $sale;
  protected $dr;
  protected $ds;
  protected $bb;

  public function __construct(SalesmtdRepo $sale, BBRepo $bbrepo, DateRange $dr, DSRepo $ds) {
    $this->sale = $sale;
    $this->dr = $dr;
    $this->bb = $bbrepo;
    $this->ds = $ds;
    $this->bb->pushCriteria(new BossBranchCriteria);
    $this->branch = new BranchRepository;
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

    $sales = null;
    if ($this->dr->fr->eq($this->dr->to))
        $sales = $this->sale->skipCache()->byDateRange($this->dr)->findWhere($where);

    $ds = $this->ds
          //->skipCache()
          ->sumByDateRange($this->dr->fr->format('Y-m-d'), $this->dr->to->format('Y-m-d'))
          ->findWhere(['branchid'=>$branch->id])->all();

    $groupies = $this->aggregateGroupies($this->sale->brGroupies($this->dr)->findWhere($where));

    $menucatid = (app()->environment()==='production') 
      ? 'E83A9DAEBC3711E6856EC3CDBB4216A7'
      : '614D4411BDF211E6978200FF18C615EC';
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

   
    

  	return $this->setDailyViewVars('product.sales.daily', $branch, $bb, $filter, $sales, $ds[0], $products, $prodcats, $menucats, $groupies, $mps);
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

      if ($value['grsamt'] > 0 && $value['qty'] > 0) {
        
        if(array_key_exists($value['productcode'],  $arr['ordered'])) {
          $arr['ordered'][$value['productcode']]['qty']     += $value['qty'];
          $arr['ordered'][$value['productcode']]['grsamt']  += $value['grsamt'];
        } else {
          $arr['ordered'][$value['productcode']]['productcode'] = $value['productcode'];
          $arr['ordered'][$value['productcode']]['product']     = $value['product'];
          $arr['ordered'][$value['productcode']]['qty']         = $value['qty'];
          $arr['ordered'][$value['productcode']]['grsamt']      = $value['grsamt'];
        }
      } else {
        array_push($arr['cancelled'], $value);
      }
    }

    return $arr;
  }



  private function setDailyViewVars($view, $branch=null, $branches=null, $filter=null, $sales=null, $ds=null, $products=null, $prodcats=null, $menucats=null, $groupies=null, $mps=null) {

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
      : '614D4411BDF211E6978200FF18C615EC';
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
      
      $products = Product::where('descriptor', 'like', '%'.$q.'%')->orderBy('descriptor')->get(['descriptor', 'id']);
      foreach ($products as $product) {
        array_push($arr, ['table'=>'product', 'item'=>$product->descriptor, 'id'=>strtolower($product->id)]);
      }

      $prodcats = Prodcat::where('descriptor', 'like', '%'.$q.'%')->orderBy('descriptor')->get(['descriptor', 'id']);
      foreach ($prodcats as $prodcat) {
        array_push($arr, ['table'=>'prodcat', 'item'=>$prodcat->descriptor, 'id'=>strtolower($prodcat->id)]);
      }

      $menucats = Menucat::where('descriptor', 'like', '%'.$q.'%')->orderBy('descriptor')->get(['descriptor', 'id']);
      foreach ($menucats as $menucat) {
        array_push($arr, ['table'=>'menucat', 'item'=>$menucat->descriptor, 'id'=>strtolower($menucat->id)]);
      }

      

    }

    return $arr;


    if($request->ajax())
      return $arr;
    else
      return abort('404');

   }







}
