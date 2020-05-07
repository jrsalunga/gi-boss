<?php namespace App\Repositories;

use Exception;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Traits\Repository as RepoTrait;
use Illuminate\Http\Request;

class LessorRepository extends BaseRepository implements CacheableInterface
{
  use CacheableRepository, RepoTrait;

  protected $order = ['code', 'descriptor'];
  
  public function model() {
    return 'App\\Models\\Lessor';
  }

  public function index_data(Request $request) {
		return $this->with('branches')->paginate($this->getLimit($request));
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