<?php namespace App\Repositories\Boss;

use Exception;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Traits\Repository as RepoTrait;
use Prettus\Repository\Events\RepositoryEntityCreated;
use Illuminate\Http\Request;

class SectorRepository extends BaseRepository implements CacheableInterface
{
  use CacheableRepository, RepoTrait;

  protected $order = ['code', 'descriptor'];
  
  public function model() {
    return 'App\\Models\\Boss\\Sector';
  }


  public function parents() {
  	return $this->scopeQuery(function($query) {
  		return $query->whereNull('parent_id')
  								->orWhere('parent_id', '=', '')
  								->orderBy('code');
		})->all();
  }


  public function index_data(Request $request) {
		return $this->scopeQuery(function($query) {
  		return $query->whereNull('parent_id')
  								->orWhere('parent_id', '=', '')
  								->orderBy('code');
		})
		->paginate($this->getLimit($request));
	}

	private function getLimit(Request $request, $limit = 10) {

		if ( $request->has('limit')
		&& filter_var($request->input('limit'), FILTER_VALIDATE_INT, ['options'=>['min_range'=>1, 'max_range'=>100]]) ) {
			return $request->input('limit');
		} else {
			return $limit;
		}
	}

  


  
	

}