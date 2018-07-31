<?php namespace App\Models;

use App\Models\BaseModel;

class Lessor extends BaseModel {

	protected $table = 'lessor';
  public $timestamps = false;
  protected $fillable = ['code', 'descriptor', 'trade_name', 'address', 'email', 'tin'];
  //protected $guarded = ['id'];
  

	public function contacts() {
    return $this->morphMany('App\Models\Contact', 'contactable');
  }


  public function branches() {
    return $this->hasMany('App\Models\Boss\Branch')->orderBy('code');
  }
  
}
