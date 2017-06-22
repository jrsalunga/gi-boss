<?php namespace App\Repositories\Criterias; 

use Prettus\Repository\Contracts\RepositoryInterface; 
use Prettus\Repository\Contracts\CriteriaInterface;
use Auth;

class BossOldBranchesCriteria implements CriteriaInterface {

	protected $branchids;

	public function __construct($branchids) {
		$this->branchids = $branchids;
	}

  public function apply($model, RepositoryInterface $repository)
  {
      $model = $model->whereIn('branchid', $this->branchids);
      return $model;
  }
}