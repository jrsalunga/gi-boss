<?php namespace App\Repositories\Criterias; 

use Prettus\Repository\Contracts\RepositoryInterface; 
use Prettus\Repository\Contracts\CriteriaInterface;
use Auth;

class LimitCriteria implements CriteriaInterface {

	protected $limit;

	public function __construct($limit = 10){
		$this->limit = $limit;
	}

  public function apply($model, RepositoryInterface $repository)
  {
      $model = $model->take($this->limit);
      return $model;
  }
}