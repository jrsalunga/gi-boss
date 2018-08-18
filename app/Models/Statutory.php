<?php namespace App\Models;

use App\Models\BaseModel;

class Statutory extends BaseModel {

	protected $table = 'statutory';
  protected $guarded = ['id'];
  protected $dates = ['date_reg'];

  public function branch() {
    return $this->belongsTo('App\Models\Employee');
  }
  
}
