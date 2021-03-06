<?php namespace App\Repositories;
use DB;
use Carbon\Carbon;
use App\Repositories\Repository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Traits\Itemizable;
use App\Models\Branch;
use App\Repositories\DateRange;


//class Purchase2Repository extends BaseRepository implements CacheableInterface
class Purchase2Repository extends BaseRepository 
{
  //use CacheableRepository;
  use Itemizable;

	public function __construct() {
    parent::__construct(app());

  }

	public function model() {
    return 'App\Models\Purchase2';
  }

  

  public function deleteWhere(array $where){
  	return $this->model->where($where)->delete();
  }

  public function branchByDR(Branch $branch, DateRange $dr) {
    return $dss = $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                    ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplierid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->leftJoin('expscat', 'expscat.id', '=', 'expense.expscatid')
                    ->select('purchase.*', 'component.code as componentcode', 'component.descriptor as component', 'component.uom as uom',
                        'supplier.code as suppliercode', 'supplier.descriptor as supplier',
                        'compcat.code as compcatcode', 'compcat.descriptor as compcat', 
                        'expense.code as expensecode', 'expense.descriptor as expense',
                        'expscat.code as expscatcode', 'expscat.descriptor as expscat')
                    ->orderBy('purchase.date', 'asc')
                    ->orderBy('component.descriptor', 'asc');
    });
  }


  public function branchGroupByDrPlain(Branch $branch, DateRange $dr) {
    return $dss = $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->orderBy('purchase.date', 'asc');
    });
  }

  public function branchGroupByDr(Branch $branch, DateRange $dr) {
    return $dss = $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->select(DB::raw('date, sum(qty) as qty, sum(tcost) as tcost'))
                    ->groupBy('date');
    });
  }


  public function brComponentByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                    ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplierid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->leftJoin('expscat', 'expscat.id', '=', 'expense.expscatid')
                    ->select(DB::raw('component.descriptor as component, count(purchase.componentid) as tran_cnt, sum(purchase.qty) as qty, sum(purchase.tcost) as tcost'))
                    ->groupBy('purchase.componentid')
                    ->orderBy('purchase.date', 'asc');
                    //->orderBy(DB::raw('sum(purchase.tcost)'), 'desc');
    });
  }

  public function brCompCatByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                    ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplierid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->leftJoin('expscat', 'expscat.id', '=', 'expense.expscatid')
                    ->select(DB::raw('compcat.descriptor as compcat, sum(purchase.qty) as qty, sum(purchase.tcost) as tcost, expense.code as expensecode'))
                    ->groupBy('compcat.id')
                    ->orderBy(DB::raw('sum(purchase.tcost)'), 'desc');
    });
  }

  public function brExpenseByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                    ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplierid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->leftJoin('expscat', 'expscat.id', '=', 'expense.expscatid')
                    ->select(DB::raw('expense.code as expensecode, expense.descriptor as expense, sum(purchase.qty) as qty, sum(purchase.tcost) as tcost, expscat.code as expscatcode'))
                    ->groupBy('expense.id')
                    ->orderBy(DB::raw('sum(purchase.tcost)'), 'desc');
    });
  }

  public function brExpsCatByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                    ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplierid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->leftJoin('expscat', 'expscat.id', '=', 'expense.expscatid')
                    ->select(DB::raw('expscat.code as expscatcode, expscat.descriptor as expscat, sum(purchase.qty) as qty, sum(purchase.tcost) as tcost'))
                    ->groupBy('expscat.id')
                    ->orderBy(DB::raw('sum(purchase.tcost)'), 'desc');
    });
  }

  public function brSupplierByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                    ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplierid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->leftJoin('expscat', 'expscat.id', '=', 'expense.expscatid')
                    ->select(DB::raw('supplier.code as code, supplier.descriptor, sum(purchase.qty) as qty, sum(purchase.tcost) as tcost, purchase.terms as terms, supplier.id as id'))
                    ->groupBy('supplier.id')
                    ->orderBy(DB::raw('sum(purchase.tcost)'), 'desc');
    });
  }

  public function brSupplierInvoiceByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                    ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplierid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->leftJoin('expscat', 'expscat.id', '=', 'expense.expscatid')
                    ->select(DB::raw('supplier.code as code, supplier.descriptor, sum(purchase.qty) as qty, sum(purchase.tcost) as tcost, purchase.terms as terms, supplier.id as id, purchase.paytype as paytype, purchase.supprefno as supprefno, purchase.date as date, purchase.save as save, purchase.branchid as branchid, purchase.supplierid as supplierid'))
                    ->groupBy('purchase.supplierid')
                    ->groupBy('purchase.supprefno')
                    ->groupBy('purchase.date')
                    //->groupBy('supplier.id')
                    ->orderBy('supplier.descriptor')
                    ->orderBy('supplier.code')
                    ->orderBy('purchase.date')
                    ->orderBy('purchase.supprefno')
                    ->orderBy(DB::raw('sum(purchase.tcost)'), 'desc');
    });
  }


  public function brPaymentByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                    ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplierid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->leftJoin('expscat', 'expscat.id', '=', 'expense.expscatid')
                    ->select(DB::raw('purchase.terms, sum(purchase.tcost) as tcost'))
                    ->groupBy('purchase.terms')
                    ->orderBy(DB::raw('sum(purchase.tcost)'), 'desc');
    });
  }


  public function componentAverageByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->where('purchase.qty', '>', 0)
                    ->where('purchase.ucost', '>', 0)
                    ->where('expense.expscatid', '7208AA3F5CF111E5ADBC00FF59FBB323')
                    ->leftJoin('hr.branch', 'branch.id', '=', 'purchase.branchid')
                    ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->select(DB::raw('branch.code, SUM(purchase.qty) as tot_qty, SUM(purchase.tcost) as tcost, (SUM(purchase.tcost)/SUM(purchase.qty)) as ave, MAX(purchase.ucost) as ucost_max, MIN(purchase.ucost) as ucost_min, MAX(purchase.qty) as qty_max, MIN(purchase.qty) as qty_min, count(purchase.id) as trancnt, purchase.branchid'))
                    ->groupBy('purchase.branchid')
                    ->orderBy(DB::raw('4'));
                    //->orderBy('branch.code');
    });
  }


  public function aggCompByDr(Carbon $fr, Carbon $to, $branchid) {
    return $this->scopeQuery(function($query) use ($fr, $to, $branchid) {
      return $query
                ->select(DB::raw('componentid, sum(qty) as qty, sum(tcost) as tcost, count(id) as trans'))
                ->whereBetween('date', 
                  [$fr->format('Y-m-d'), $to->format('Y-m-d')]
                  )
                ->where('branchid', $branchid)
                ->groupBy('componentid');
    })->all();
  }

  public function aggExpByDr(Carbon $fr, Carbon $to, $branchid) {
    return $this->scopeQuery(function($query) use ($fr, $to, $branchid) {
      return $query
                ->select(DB::raw('compcat.expenseid as expense_id, sum(purchase.qty) as qty, sum(purchase.tcost) as tcost, count(purchase.id) as trans, expense.ordinal as ordinal'))
                ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                ->whereBetween('purchase.date', 
                  [$fr->format('Y-m-d'), $to->format('Y-m-d')]
                  )
                ->where('purchase.branchid', $branchid)
                ->groupBy('compcat.expenseid');
    })->all();
  }

  public function findInvoicesWhere(array $where) {
    return $this->scopeQuery(function($query)  {
      return $query
                ->select(DB::raw('purchase.date, purchase.terms, purchase.supprefno, purchase.supplierid, purchase.branchid, sum(purchase.qty) as qty, sum(purchase.tcost) as tcost, supplier.descriptor as supplier, supplier.code as suppliercode, branch.code as branchcode, branch.descriptor as branch, count(purchase.id) as count'))
                ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplierid')
                ->leftJoin('branch', 'branch.id', '=', 'purchase.branchid')
                ->groupBy('branchid')
                ->groupBy('supplierid')
                ->groupBy('supprefno')
                ->orderBy('branch.code')
                ->orderBy('supplier.descriptor')
                ->orderBy('purchase.date');
    })->findWhere($where);
  }




  

}