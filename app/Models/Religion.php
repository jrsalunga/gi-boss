<?php namespace App\Models;

use App\Models\BaseModel;

class Religion extends BaseModel {

	protected $table = 'religion';
 	protected $fillable = ['code', 'descriptor'];
 	public static $header = ['code', 'descriptor'];


 	public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);
    if (app()->environment()==='production')
      $this->setConnection('mysql-hr');
    else  
      $this->setConnection('hr-live');
  }

	
  
}