<?php namespace App\Models;

use App\Models\BaseModel;

class Contact extends BaseModel {

	protected $table = 'contact';
  public $timestamps = false;
  protected $fillable = ['type', 'number'];
  //protected $guarded = ['id'];
  

	public function contactable() {
    return $this->morphTo();
  }
  
}
