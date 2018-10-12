<?php namespace App\Models;

use App\Models\BaseModel;

class MonthProduct extends BaseModel {

	protected $table = 'month_product';
	public $timestamps = false;
	//protected $appends = ['date'];
  protected $dates = ['date'];
	protected $guarded = ['id'];
	protected $casts = [
    'netamt' => 'float',
    'qty' => 'float',
    'trans' => 'integer'
  ];

	public function product() {
    return $this->belongsTo('App\Models\Product');
  }

  public function branch() {
    return $this->belongsTo('App\Models\Branch')->select(['code', 'descriptor', 'id']);
  }

  
 

 
	
  
}