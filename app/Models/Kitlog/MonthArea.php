<?php namespace App\Models\Kitlog;

use App\Models\BaseModel;

class MonthArea extends BaseModel {

	protected $table = 'month_kitlog_area';
  protected $dates = ['date'];
	protected $guarded = ['id'];
	protected $casts = [
    'qty' => 'float',
    'ave' => 'float',
    'max' => 'float',
    'min' => 'float',
    'iscombo' => 'boolean',
    'rank' => 'integer',
  ];

	public function product() {
    return $this->belongsTo('App\Models\Product');
  }

  public function branch() {
    return $this->belongsTo('App\Models\Branch')->select(['code', 'descriptor', 'id']);
  }

  
 

 
	
  
}