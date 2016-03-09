<?php namespace App\Repositories;

use App\Repositories\Repository;
use Prettus\Repository\Eloquent\BaseRepository;

use Illuminate\Support\Collection;
use Illuminate\Container\Container as App;

use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;


class BranchRepository extends BaseRepository implements CacheableInterface
//class BranchRepository extends BaseRepository 
{
  use CacheableRepository;

	public function __construct(App $app, Collection $collection) {
      parent::__construct($app, $collection);

			$this->boot();      
  }

  public function boot() {
  	$this->scopeQuery(function($query){
  		return $query->whereNotIn('id', ['971077BCA54611E5955600FF59FBB323', '3C561250F87448E3A2DD0562B24E3639'])
  								->orderBy('code','asc');
		});
  }

	public function model() {
    return 'App\\Models\\Branch';
  }

  
 




  
  

    




}