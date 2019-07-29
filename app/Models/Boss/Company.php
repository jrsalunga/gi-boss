<?php namespace App\Models\Boss;

use App\Models\BaseModel;

class Company extends BaseModel {

	//protected $connection = 'mysql-hr';
	protected $table = 'company';
	//protected $fillable = ['code', 'descriptor', 'address', 'email', 'tin', 'sss_no', 'philhealth_no', 'hdmf_no'];
  protected $guarded = ['id'];
  
  

	public function branches() {
    return $this->hasMany('App\Models\Boss\Branch')
              ->select(['code', 'descriptor', 'company_id', 'tin', 'status', 'id'])
              ->orderBy('code');
  }

  public function branches_tin() {
    return $this->hasMany('App\Models\Boss\Branch')
              ->select(['code', 'descriptor', 'company_id', 'tin', 'status', 'id'])
              ->orderBy('tin');
  }

  public function contacts() {
    return $this->morphMany('App\Models\Contact', 'contactable');
  }

  
  
}
