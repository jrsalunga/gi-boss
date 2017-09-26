<?php namespace App\Models;

use App\Models\BaseModel;

class StockTransfer extends BaseModel {

	protected $table = 'stocktransfer';
  public $timestamps = false;
  protected $guarded = ['id'];
  protected $dates = ['date'];

  protected $casts = [
    'qty' => 'float',
    'ucost' => 'float',
    'tcost' => 'float',
    'vat' => 'float'
  ];

  public function branch() {
    return $this->belongsTo('App\Models\Branch', 'branchid');
  }

  public function component() {
    return $this->belongsTo('App\Models\Component', 'componentid');
  }

  public function supplier() {
    return $this->belongsTo('App\Models\Supplier', 'supplierid');
  }

  public function toSupplier() {
    return $this->belongsTo('App\Models\Supplier', 'to');
  }

  public function toBranch() {
    return $this->belongsTo('App\Models\Branch', 'to');
  }
  
}
