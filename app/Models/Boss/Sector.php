<?php namespace App\Models\Boss;

use App\Models\BaseModel;

class Sector extends BaseModel {

	protected $table = 'sector';
 	protected $fillable = ['code', 'descriptor', 'parent_id', 'am_id', 'kh_id'];
 	protected $dates = ['deleted_at', 'created_at', 'updated_at'];

	public function parent() {
    return $this->belongsTo('App\Models\Boss\Sector', 'parent_id');
  }

  public function am() {
    return $this->belongsTo('App\Models\Employee', 'am_id')
              ->select(['firstname', 'lastname', 'middlename', 'positionid', 'email', 'mobile', 'id'])
              ->with('position');
  }

  public function kh() {
    return $this->belongsTo('App\Models\Employee', 'kh_id')
              ->select(['firstname', 'lastname', 'middlename', 'positionid', 'email', 'mobile', 'id'])
              ->with('position');
  }

  public function children() {
    return $this->hasMany('App\Models\Boss\Sector', 'parent_id', 'id')->orderBy('code');
  }

  public function branch() {
    return $this->hasMany('App\Models\Boss\Branch')
            ->where('status','<','3')
            ->orderBy('code');
  }

  public function is_parent() {
  	return empty($this->parent_id) ? true : false;
  }

  public function branch_count() {
    $ctr = 0;
    foreach ($this->children as $key => $child) {
      foreach ($child->branch as $key => $branch) {
        $ctr++;
      }
    }
    foreach ($this->branch as $key => $br) {
      $ctr++;
    }
    return $ctr;
  }

  
}
