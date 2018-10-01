<?php namespace App\Repositories\Criterias; 

use Prettus\Repository\Contracts\RepositoryInterface; 
use Prettus\Repository\Contracts\CriteriaInterface;

class SqlSelect implements CriteriaInterface {

	protected $fields;

	// last on push criteria
	public function __construct($fields = ['*']) {
		$this->fields = $fields;
	}

  public function apply($model, RepositoryInterface $repository)
  {
      return $model->select($this->fields);
  }
}