<?php namespace App\Models;

use App\Models\BaseModel;

class Acadlevel extends BaseModel {
 
	//protected $connection = 'hr';
	protected $table = 'acadlevel';
 	protected $fillable = ['code', 'descriptor'];

  public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);
    if (app()->environment()==='production')
      $this->setConnection('mysql-hr');
    else  
      $this->setConnection('hr-live');
  }
}
