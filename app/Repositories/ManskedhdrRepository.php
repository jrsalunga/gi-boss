<?php namespace App\Repositories;

use App\Traits\Repository as RepoTrait;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;

class ManskedhdrRepository extends BaseRepository implements CacheableInterface
{
  use CacheableRepository, RepoTrait;

  protected $order = ['year', 'weekno'];

  protected $fieldSearchable = [
    'branch.code',
    'date'
  ];

  public function model() {
    return 'App\\Models\\Manskedhdr';
  }

  public function boot(){
    $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
  }




  
	

}