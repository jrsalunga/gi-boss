<?php namespace App\Models;

use App\Models\BaseModel;

class Statutory extends BaseModel {

	protected $table = 'statutory';
  protected $guarded = ['id'];

  public function branch() {
    return $this->belongsTo('App\Models\Employee');
  }
  
}
