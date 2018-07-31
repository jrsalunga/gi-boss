<?php namespace App\Models\Boss;

use App\Models\BaseModel;

class Sector extends BaseModel {

	protected $table = 'sector';
 	protected $fillable = ['code', 'descriptor', 'parent_id'];
 	protected $dates = ['deleted_at', 'created_at', 'updated_at'];

	public function parent() {
    return $this->belongsTo('App\Models\Boss\Sector', 'parent_id');
  }

  public function children() {
    return $this->hasMany('App\Models\Boss\Sector', 'parent_id', 'id')->orderBy('code');
  }

  public function is_parent() {
  	return empty($this->parent_id) ? true : false;
  }

  
}
