<?php namespace App\Repositories\Criterias; 

use Prettus\Repository\Contracts\RepositoryInterface; 
use Prettus\Repository\Contracts\CriteriaInterface;
use App\Repositories\DateRange as DR;

class DateRange implements CriteriaInterface {

	protected $dr;

	public function __construct(DR $dr){
		$this->dr = $dr;
	}

  public function apply($model, RepositoryInterface $repository)
  {
      return $model->whereBetween('date', [$this->dr->fr->format('Y-m-d'), $this->dr->to->format('Y-m-d')]);
  }
}