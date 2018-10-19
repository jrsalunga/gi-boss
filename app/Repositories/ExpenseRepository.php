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

  public $expense_array = ["CK","FS","FV","GR","MP","RC","SS"]; // no "DN","DB","DA","CG","IC"
  public $non_cos_array = ["DB","DA","DN","CG","IC"];




	public function model() {
    return 'App\Models\Expense';
  }


  public function getCos() {
    return $this->scopeQuery(function($query) {
      return $query->whereIn('code', $this->expense_array)
                    ->select(DB::raw('code, descriptor, ordinal, expscatid, id'))
                    ->orderBy('ordinal');
    })->all();

  }

  public function getNonCos() {
    return $this->scopeQuery(function($query) {
      return $query->whereIn('code', $this->non_cos_array)
                    ->select(DB::raw('code, descriptor, ordinal, expscatid, id'))
                    ->orderBy('ordinal');
    })->all();

  }

 

}