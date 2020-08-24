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







  

  
  
	

}