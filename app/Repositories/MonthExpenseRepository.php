<?php namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Traits\Repository as RepoTrait;
use Carbon\Carbon;
use DB;

class MonthExpenseRepository extends BaseRepository implements CacheableInterface
{
  use CacheableRepository, RepoTrait;
  
  protected $order = ['date'];

  public function model() {
    return 'App\\Models\\MonthExpense';
  }

  public function sumCosByDr($branchid, Carbon $fr, Carbon $to, $ids) {
  	return $this->scopeQuery(function($query) use ($ids, $fr, $to) {
					      return $query->whereIn('expense_id', $ids)
					      						->whereBetween('date', [$fr->format('Y-m-d'), $to->format('Y-m-d')])
  													->select(DB::raw('sum(tcost) as tcost, expense_id'))
  													->groupBy('expense_id');
					    })
							->findWhere(['branch_id'=>$branchid]);
  }


  

  
  
	

}