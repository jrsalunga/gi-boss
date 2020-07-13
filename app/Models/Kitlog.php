<?php namespace App\Models;

use App\Models\BaseModel;

class Kitlog extends BaseModel {

	protected $table = 'kitlog';
	public $timestamps = false;
  protected $dates = ['date'];
	protected $guarded = ['id'];
	protected $casts = [
    'minute' => 'float',
    'iscombo' => 'boolean',
  ];
}