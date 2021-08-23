<?php namespace App\Repositories\Criterias; 

use Prettus\Repository\Contracts\RepositoryInterface; 
use Prettus\Repository\Contracts\CriteriaInterface;
use Auth;

class ActiveBossBranchCriteria implements CriteriaInterface {

	protected $fields;

	public function __construct($fields = ['*']) {
		$this->fields = $fields;
	}

  public function apply($model, RepositoryInterface $repository)
  {
      $model = $model
                ->select($this->fields)
      					->whereIn('status', [2,4])
      					->whereIn('type', [0,1,2,3])
      					->orderBy('code');
      return $model;
  }
}