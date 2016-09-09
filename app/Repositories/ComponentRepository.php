<?php namespace App\Repositories;

use App\Repositories\Repository;
use Prettus\Repository\Eloquent\BaseRepository;

use Illuminate\Support\Collection;
use Illuminate\Container\Container as App;

use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Criteria\RequestCriteria;


class ComponentRepository extends BaseRepository implements CacheableInterface
//class ComponentRepository extends BaseRepository 
{
  use CacheableRepository;

	public function __construct() {
      parent::__construct(app());
  }

  

	public function model() {
    return 'App\\Models\\Component';
  }

  protected $fieldSearchable = [
    'descriptor'=>'like',
  ];

  




  
  

    




}