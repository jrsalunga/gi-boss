<?php namespace App\Models\Boss;

use Carbon\Carbon;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends BaseModel {

  use SoftDeletes;

  // protected $connection = 'boss-live';
	protected $table = 'branch';
  //protected $fillable = ['code', 'descriptor'];
 	protected $guarded = ['id'];
  public $timestamps = true;
  protected $dates = ['created_at', 'updated_at', 'deleted_at', 'date_reg', 'date_start', 'date_end'];

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
    return $this->belongsTo('App\Models\Boss\Company');
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


  public function scopeActive($query) {
    return $query->where('status', 2);
  }


  
  

  


  public function getRouteKey() {
      return $this->slug;
  }

  public function lcode() {
    return strtolower($this->code);
  }

  public function fullAddress() {
    $u = $this->spaces;
    $ctr = count($u);
    if ($ctr>0) {
      $a = '';
      foreach ($u as $key => $s) {
        if ($key==0)
          $a = $s->unit;
        else if (($ctr-1)==$key)
          $a = $a.' and '.$s->unit;
        else
          $a = $a.', '.$s->unit;
        # code...
      }
      return $a.', '.$this->address;
    }
    return $this->address;
  }


  public function service_period($format=false) {
    $fr = $to = NULL;
    $mon = 0;
    $res = NULL;
    $y = $m = '';

    if (!is_null($this->date_start) && is_iso_date($this->date_start->format('Y-m-d')))
      $fr = $this->date_start;
    if (!is_null($this->date_end) && is_iso_date($this->date_end->format('Y-m-d')))
      $to = $this->date_end;

    if (!is_null($fr)) {

      $to = is_null($to) ? Carbon::now() : $to;

      if ($format) {

        $yr = floor($fr->diffInMonths($to)/12);
        $mon = $fr->diffInMonths($to) % 12;

        if ($yr>0) {
          $s = $yr > 1 ? 'yrs':'yr';
          $y = $yr.$s;
        }

        if ($mon>0) {
          $s = $mon > 1 ? 'mons':'mon';
          $m = $mon.$s;
        }        

        return empty($y) && empty($m) ? NULL : trim($y.' '.$m); 
      } else {
        return $fr->diffInMonths($to);
      }
    }
    return NULL;
  }
  
}
