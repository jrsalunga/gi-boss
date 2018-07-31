<?php namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;

class Education extends BaseModel {
 
	//protected $connection = 'hr';
	protected $table = 'education';
 	protected $fillable = ['employeeid', 'school'];

	public function employee() {
    return $this->belongsTo('App\Models\Employee', 'employeeid');
  }

  public function acadlevel() {
    return $this->belongsTo('App\Models\Acadlevel', 'acadlvlid');
  }

  public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);
    if (app()->environment()==='production')
      $this->setConnection('mysql-hr');
    else  
      $this->setConnection('hr-live');
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
