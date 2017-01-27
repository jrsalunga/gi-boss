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

       $sales = $this->sale
                //->skipCache()
                ->byDateRange($this->dr)
                ->findWhere($where);

		} else {
      $sales = null;
			$filter->table = '';
			$filter->id = '';
			$filter->item = '';
		}


  	$bb = $this->bossBranch();
    $this->dr->setDateRangeMode($request, 'daily');

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
          //->skipCache()
          ->sumByDateRange($this->dr->fr->format('Y-m-d'), $this->dr->to->format('Y-m-d'))
          ->findWhere(['branchid'=>$branch->id])->all();
    
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

   
    

  	return $this->setDailyViewVars('product.sales.daily', $branch, $bb, $filter, $sales, $ds[0], $products, $prodcats, $menucats);
  }



  private function setDailyViewVars($view, $branch=null, $branches=null, $filter=null, $sales=null, $ds=null, $products=null, $prodcats=null, $menucats=null) {

    return $this->setViewWithDR(view($view)
                ->with('branch', $branch)
                ->with('branches', $branches)
                ->with('filter', $filter)
                ->with('sales', $sales)
                ->with('ds', $ds)
                ->with('products', $products)
                ->with('prodcats', $prodcats)
                ->with('menucats', $menucats));
  }



  private function setViewWithDR($view){
    $response = new Response($view->with('dr', $this->dr));
    $response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
    $response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
    $response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
    return $response;
  }



}
