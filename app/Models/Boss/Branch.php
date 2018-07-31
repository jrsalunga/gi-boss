<?php namespace App\Models\Boss;

use Carbon\Carbon;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends BaseModel {

  use SoftDeletes;

  //protected $connection = 'mysql-hr';
	protected $table = 'branch';
  //protected $fillable = ['code', 'descriptor'];
 	protected $guarded = ['id'];
  public $timestamps = true;
  protected $dates = ['created_at', 'updated_at', 'deleted_at', 'reg_date'];

	public function employee() {
    return $this->hasMany('App\Models\Employee', 'employeeid');
  }

  public function holidays() {
    return $this->hasMany('App\Models\Holidaydtl', 'branchid');
  }

  public function dailysales() {
    return $this->hasMany('App\Models\DailySales', 'branchid');
  }

  public function company() {
    return $this->belongsTo('App\Models\Company');
  }

  public function lessor() {
    return $this->belongsTo('App\Models\Lessor');
  }

  public function sector() {
    return $this->belongsTo('App\Models\Boss\Sector');
  }

  public function boss() {
    return $this->hasMany('App\Models\BossBranch', 'branchid');
  }

  public function contacts() {
    return $this->morphMany('App\Models\Contact', 'contactable');
  }

  public function spaces() {
    return $this->hasMany('App\Models\Boss\Space');
  }






  /***************** mutators *****************************************************/
  
  public function getRegDateAttribute($value){
    if (is_null($value))
      return '';

    if (empty($value))
      return '';

    if (!is_iso_date($value))
      return '';

    try {
      Carbon::parse($value);
    } catch (\Exception $e) {
      return '';      
    }
    return $value;
  }
  

  


  public function getRouteKey() {
      return $this->slug;
  }

  public function lcode() {
    return strtolower($this->code);
  }
  
}
