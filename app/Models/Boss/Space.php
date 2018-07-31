<?php namespace App\Models\Boss;

use App\Models\BaseModel;

class Space extends BaseModel {

	protected $table = 'space';
 	protected $fillable = ['unit', 'area', 'branch_id'];

	public function branch() {
    return $this->belongsTo('App\Models\Branch');
  }

  
}
