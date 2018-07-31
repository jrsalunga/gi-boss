<?php namespace App\Repositories;

use Exception;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Traits\Repository as RepoTrait;

class StatutoryRepository extends BaseRepository implements CacheableInterface
//class StatutoryRepository extends BaseRepository 
{
  use CacheableRepository, RepoTrait;

  protected $order = ['id'];
  
  public function model() {
    return 'App\\Models\\Statutory';
  }



  
	

}