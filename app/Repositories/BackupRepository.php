<?php namespace App\Repositories;

use App\Repositories\Repository;
use Prettus\Repository\Eloquent\BaseRepository;

use Illuminate\Support\Collection;
use Illuminate\Container\Container as App;

use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;


//class BackupRepository extends BaseRepository implements CacheableInterface
class BackupRepository extends BaseRepository 
{
  //use CacheableRepository;

	public function __construct(App $app, Collection $collection) {
      parent::__construct($app, $collection);

      
  }


	public function model() {
    return 'App\\Models\\Backup';
  }


  

    




}