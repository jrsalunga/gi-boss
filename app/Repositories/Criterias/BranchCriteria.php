<?php namespace App\Repositories\Criterias; 

use App\Models\Branch;
use Prettus\Repository\Contracts\RepositoryInterface; 
use Prettus\Repository\Contracts\CriteriaInterface;

class BranchCriteria implements CriteriaInterface {

	private $branch;

  public function __construct(Branch $branch){
      $this->branch = $branch;
  }

  public function apply($model, RepositoryInterface $repository)
  {
      //$branchids = $repository->bossbranch->all()->pluck('branchid');
      $model = $model->where('branchid', $this->branch->id);
      return $model;
  }
}