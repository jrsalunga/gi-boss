<?php namespace App\Repositories\Criterias; 

use Prettus\Repository\Contracts\RepositoryInterface; 
use Prettus\Repository\Contracts\CriteriaInterface;
use Auth;

class ActiveBranchCriteria implements CriteriaInterface {

	protected $fields;

	public function __construct($fields = ['*']) {
		$this->fields = $fields;
	}

  public function apply($model, RepositoryInterface $repository)
  {
      $model = $model->select($this->fields)
      							->where('opendate', '<>', '0000-00-00')
      							->where('closedate', '=', '0000-00-00')
      							->orderBy('code');
      return $model;
  }
}