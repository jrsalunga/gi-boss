<?php namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;

class ManskeddtlRepository extends BaseRepository implements CacheableInterface
{
  use CacheableRepository;

  public function model() {
    return 'App\\Models\\Manskeddtl';
  }




  public function allTimelogByDate(Carbon $date, $branchid) {

    return $this->scopeQuery(function($query) use ($date, $branchid){
        return $query->whereBetween('datetime', [
                      $date->copy()->format('Y-m-d').' 06:00:00',          // '2015-11-13 06:00:00'
                      $date->copy()->addDay()->format('Y-m-d').' 05:59:59' // '2015-11-14 05:59:59'
                    ])
                  ->where('ignore', 0)
                  ->where('branchid', $branchid)
                  ->orderBy('datetime', 'ASC')
                  ->orderBy('txncode', 'ASC');
      });
  }




  
	

}