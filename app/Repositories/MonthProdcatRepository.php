<?php namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Traits\Repository as RepoTrait;
use Carbon\Carbon;
use DB;

class MonthProdcatRepository extends BaseRepository implements CacheableInterface
{
  use CacheableRepository, RepoTrait;
  
  protected $order = ['date'];

  public function model() {
    return 'App\\Models\\MonthProdcat';
  }

  public function sumSalesByProdcatDr($branchid, Carbon $fr, Carbon $to, $id) {
  	return $this
                ->scopeQuery(function($query) use ($branchid, $id, $fr, $to) {
					      return $query->where('prodcat_id', $id)
                            ->where('branch_id', $branchid)
					      						->whereBetween('date', [$fr->format('Y-m-d'), $to->format('Y-m-d')])
  													->select(DB::raw('sum(sales) as sales'));
					    })->skipCache()->all();
  }


  public function allByMonth($branchid, Carbon $fr, Carbon $to) {
    return $this
                ->scopeQuery(function($query) use ($branchid, $fr, $to) {
                return $query->where('branch_id', $branchid)
                            ->whereBetween('date', [$fr->format('Y-m-d'), $to->format('Y-m-d')]);
              })->skipCache()->all();
  }

  
  public function _allByMonth($branchid, Carbon $fr, Carbon $to) {
    return $this
                ->scopeQuery(function($query) use ($branchid, $fr, $to) {
                return $query->where('month_prodcat.branch_id', $branchid)
                            ->whereBetween('month_prodcat.date', [$fr->format('Y-m-d'), $to->format('Y-m-d')])
                            ->leftJoin('prodcat', 'prodcat.id', '=', 'month_prodcat.prodcat_id')
                            ->select(DB::raw('month_prodcat.date, month(month_prodcat.date) as month, prodcat.descriptor as prodcat, month_prodcat.sales'));
              })->skipCache()->all();
  }
  
  
	

}