<?php namespace App\Models;

use App\Models\BaseModel;

class BossBranch extends BaseModel {

  //protected $connection = 'mysql-hr';
	protected $table = 'bossbranch';
 	protected $fillable = ['bossid', 'branchid'];

	public function branch() {
    return $this->belongsTo('App\Models\Branch', 'branchid');
  }

  
  
}
