<?php namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;


use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;


class BossBranchRepository extends BaseRepository implements CacheableInterface
//class BossBranchRepository extends BaseRepository 
{
  use CacheableRepository;



  /**
   * Specify Model class name
   *
   * @return string
   */
  function model()
  {
      return "App\\Models\\BossBranch";
  }
}