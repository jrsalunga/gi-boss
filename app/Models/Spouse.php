<?php namespace App\Models;

use App\Models\BaseModel;

class Spouse extends BaseModel {
 
	//protected $connection = 'hr';
	protected $table = 'spouse';
 	//protected $guard = ['employeeid', 'lastname', 'firstname', 'middlename', 'birthdate', 'address', 'lastname', 'phone', 'fax', 'mobile', 'email'];
  protected $guarded = ['id'];
  protected $dates = ['birthdate'];

	public function employee() {
    return $this->belongsTo('App\Models\Employee', 'employeeid');
  }

  public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);
    if (app()->environment()==='production')
      $this->setConnection('mysql-hr');
    else  
      $this->setConnection('hr-live');
  }

  public function getBirthdate() {
    return is_iso_date($this->birthdate->format('Y-m-d'))
      ? $this->birthdate->format('Y-m-d')
      : NULL;
  }

  public function setBirthdateAttribute($value){
    $this->attributes['birthdate'] = $value;
  }


 
}
