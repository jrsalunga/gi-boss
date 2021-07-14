<?php namespace App\Repositories;
use DB;
use Carbon\Carbon;
use App\Repositories\Repository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Traits\Repository as RepoTrait;


class ExpenseRepository extends BaseRepository implements CacheableInterface
{
  use CacheableRepository, RepoTrait;


	public function model() {
    return 'App\Models\Expense';
  }


  public function getCos() {
    return $this->scopeQuery(function($query) {
      return $query->whereIn('code', config('giligans.expensecode.cos'))
                    ->select(DB::raw('code, descriptor, ordinal, expscatid, id'))
                    ->orderBy('seqno');
    })->all();

  }

   public function getExpense() {
    return $this->scopeQuery(function($query) {
      return $query->where('ordinal', 'like', '8%')
                    ->select(DB::raw('code, descriptor, ordinal, expscatid, id'))
                    ->orderBy('seqno');
    })->all();

  }

  public function getNonCos() {
    return $this->scopeQuery(function($query) {
      return $query->whereIn('code', config('giligans.expensecode.ncos'))
                    ->select(DB::raw('code, descriptor, ordinal, expscatid, id'))
                    ->orderBy('seqno');
    })->all();

  }

 

}