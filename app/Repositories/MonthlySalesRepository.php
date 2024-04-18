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

    return $mss = $this->scopeQuery(function($query) use ($branch, $dr, $sql) {
      return $query->whereBetween('date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                  ->where('branch_id', $branch->id)
                  ->select(DB::raw($sql));
    })->first();
  }

  public function allBranchMonthlyCashFlow($date) {

    $date = c($date)->endOfMonth();


    return DB::table('monthlysales AS a')
              ->select(DB::raw('rm.alias, b.code, a.sales, a.ave_sales, a.totdeliver, a.pct_deliver, a.ave_deliver, (a.tot_dine+a.tot_togo) as dine_togo_sales, a.sale_csh, a.depo_cash, a.ending_csh,
c.csh_disb, c.csh_out, a.tot_dine, a.tot_togo, a.fc, b.code, a.ending_csh'))
              ->leftJoin('branch AS b', 'b.id', '=', 'a.branch_id')
              ->join('month_cshaudit AS c', function ($join) {
                $join->on('a.date', '=', 'c.date')
                     ->on('a.branch_id', '=', 'c.branch_id');
              })
              ->leftJoin('sector AS sec', 'sec.id', '=', 'b.sector_id')
              ->leftJoin('sector AS rm', 'sec.parent_id', '=', 'rm.id')
              ->where('a.date', $date->format('Y-m-d'))
              ->where('a.branch_id', '<>', '`ALL`')
              ->orderBy('rm.alias')
              ->orderBy('b.code')
              ->get();
   
  }


  public function getCustomerMonthly(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query
                  ->select(DB::raw('branch.code, date, sales, custcount, trans_cnt, branch_id'))
                  ->leftJoin('branch', 'branch.id', '=', 'monthlysales.branch_id')
                  ->whereBetween('date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                  ->where('branch_id','<>','ALL')
                  ->orderBy('branch.code')
                  ->orderBy('date', 'asc');
    })->get();
  }

  public function getCustomerYearly(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query
                  ->select(DB::raw('branch.code, date, sum(sales) as sales, sum(custcount) as custcount, sum(trans_cnt) as trans_cnt, branch_id'))
                  ->leftJoin('branch', 'branch.id', '=', 'monthlysales.branch_id')
                  ->whereBetween('date', [$dr->fr->format('Y').'-01-01', $dr->to->format('Y').'-12-31'])
                  ->where('branch_id','<>','ALL')
                  ->groupBy(DB::raw('year(date)'))
                  ->groupBy('branch_id')
                  ->orderBy('branch.code')
                  ->orderBy(DB::raw('year(date)'), 'asc');
    })->get();

  }
}