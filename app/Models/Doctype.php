<?php namespace App\Models;

use App\Models\BaseModel;

class Doctype extends BaseModel {

	protected $table = 'doctype';
	public $timestamps = false;
  // protected $dates = ['date'];
	protected $guarded = ['id'];
	// protected $casts = [
 //    'minute' => 'float',
 //    'iscombo' => 'boolean',
  // ];


}