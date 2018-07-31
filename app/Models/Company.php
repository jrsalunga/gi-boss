<?php namespace App\Models;

use App\Models\BaseModel;

class Company extends BaseModel {

	//protected $connection = 'mysql-hr';
	protected $table = 'company';
	//protected $fillable = ['code', 'descriptor', 'address', 'email', 'tin', 'sss_no', 'philhealth_no', 'hdmf_no'];
  protected $guarded = ['id'];
  
  public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);
    if (app()->environment()==='production')
      $this->setConnection('mysql-hr');
    else  
      $this->setConnection('hr-live');
  }

	public function branches() {
    return $this->hasMany('App\Models\Boss\Branch');
  }

  public function contacts() {
    return $this->morphMany('App\Models\Contact', 'contactable');
  }

  
  
}
