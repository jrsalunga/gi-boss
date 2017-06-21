<?php namespace App\Models;

use App\Models\BaseModel;

class Workexp extends BaseModel {

	//protected $connection = 'hr';
	protected $table = 'workexp';
 	protected $fillable = ['employeeid', 'company', 'position'];

	public function employee() {
    return $this->hasMany('App\Models\Employee', 'employeeid');
  }

  public function __construct(array $attributes = [])
  {
  	parent::__construct($attributes);
    $this->setConnection('mysql-hr');
  }
  
}
