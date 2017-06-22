<?php namespace App\Repositories;

use App\Traits\Repository as RepoTrait;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;

class ManskeddayRepository extends BaseRepository implements CacheableInterface
{
  use CacheableRepository, RepoTrait;

  protected $order = ['date'];

  public function model() {
    return 'App\\Models\\Manskedday';
  }




  
	

}