<?php namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Traits\Repository as RepoTrait;
use Carbon\Carbon;
use DB;

class DayProdcatRepository extends BaseRepository implements CacheableInterface
{
  use CacheableRepository, RepoTrait;
  
  protected $order = ['date'];

  public function model() {
    return 'App\\Models\\DayProdcat';
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


  

  
  
	

}