<?php namespace App\Models;

use App\Models\BaseModel;
use App\Traits\CodeCaps;

class Position extends BaseModel {

  use CodeCaps;

	protected $table = 'position';
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


	public function employee() {
    return $this->hasOne('App\Models\Employee', 'positionid');
  }
  
}
