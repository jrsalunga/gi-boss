<?php namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Repositories\ProductRepository;
use App\Traits\Repository as RepoTrait;
use Carbon\Carbon;
use App\Models\Branch;
use DB;

class MonthlySalesRepository extends BaseRepository implements CacheableInterface
{
  use CacheableRepository, RepoTrait;
  
  protected $order = ['date'];

  public function model() {
    return 'App\\Models\\MonthlySales';
  }


  public function rank($date=null) {

    if ($date instanceof Carbon)
      return $this->generateRank($date);
    elseif (is_iso_date($date))
      return $this->generateRank(c($date));
    else
      return $this->generateRank(c());
  }

  private function generateRank(Carbon $date) {
    
    $ms = $this->skipCache()
            ->scopeQuery(function($query) use ($date) {
              return $query->where(DB::raw('MONTH(date)'), $date->format('m'))
                          ->where(DB::raw('YEAR(date)'), $date->format('Y'));
            })
            ->orderBy('sales', 'DESC')
            ->all();

    if (count($ms)<=0)
      return false;
    
    foreach ($ms as $key => $m) {
      //$this->update(['rank'=>($key+1)], $m->id);
      $m->rank = ($key+1);
      if ($m->sales>0)
        $m->save();

      if ($m->sales<=0 && ($m->date->format('Y-m-d')==$m->date->copy()->lastOfMonth()->format('Y-m-d')))
        $m->delete();
    }
    return true;
    
  }




  public function aggBranchByDR(Branch $branch, DateRange $dr) {

    $sql = 'sum(sales) as sales, sum(sale_csh) as sale_csh, sum(sale_chg) as sale_chg, sum(sale_sig) as sale_sig, ';
    $sql .= 'sum(cos) as cos, sum(food_sales) as food_sales, sum(tips) as tips, sum(custcount) as custcount';

    return $mss = $this->scopeQuery(function($query) use ($dr, $sql) {
      return $query->whereBetween('date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                  ->select(DB::raw($sql));
    })->first();
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