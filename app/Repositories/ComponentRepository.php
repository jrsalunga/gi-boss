<?php namespace App\Repositories;

use App\Repositories\Repository;
use Prettus\Repository\Eloquent\BaseRepository;

use Illuminate\Support\Collection;
use Illuminate\Container\Container as App;

use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Traits\Repository as RepoTrait;


class ComponentRepository extends BaseRepository implements CacheableInterface
//class ComponentRepository extends BaseRepository 
{
  use CacheableRepository, RepoTrait;

  protected $order = ['descriptor', 'code'];

  protected $fieldSearchable = [
    'code'=>'like',
    'descriptor'=>'like',
    'compcat.descriptor'=>'like',
    'cost'=>'like',
    'uom'=>'like',
  ];

	public function __construct() {
      parent::__construct(app());
  }

  public function boot(){
    $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
  }

	public function model() {
    return 'App\\Models\\Component';
  }

  

  




  
  

    




}