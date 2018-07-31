<?php namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;

class Workexp extends BaseModel {

	//protected $connection = 'hr';
	protected $table = 'workexp';
 	protected $fillable = ['employeeid', 'company', 'position', 'periodfrom', 'periodto', 'remarks'];

  public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);
    if (app()->environment()==='production')
      $this->setConnection('mysql-hr');
    else  
      $this->setConnection('hr-live');
  }

  public function employee() {
    return $this->belongsTo('App\Models\Employee', 'employeeid');
  }

  public function getPeriodFrom() {
    $c = Carbon::parse($this->periodfrom.'-01');
    return is_iso_date($c->format('Y-m-d'))
      ? $c
      : false;
  }

  public function getPeriodTo() {
    $c = Carbon::parse($this->periodto.'-01');
    return is_iso_date($c->format('Y-m-d'))
      ? $c
      : false;
  }

  
}
