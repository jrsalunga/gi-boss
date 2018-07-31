<?php namespace App\Models;

use App\Models\BaseModel;

class Ecperson extends BaseModel {
 
	//protected $connection = 'hr';
	protected $table = 'ecperson';
 	//protected $fillable = ['employeeid', 'lastname', 'firstname', ''];
  protected $guarded = ['id'];
  protected $date = ['birthdate'];

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

  
}
