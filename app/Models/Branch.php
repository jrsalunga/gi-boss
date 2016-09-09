<?php namespace App\Models;

use App\Models\BaseModel;

class Branch extends BaseModel {

  protected $connection = 'mysql-hr';
	protected $table = 'hr.branch';
 	protected $fillable = ['code', 'descriptor'];
  protected $appends = ['active'];
 	public static $header = ['code', 'descriptor'];

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
    return $this->belongsTo('App\Models\Company', 'companyid');
  }






  /***************** mutators *****************************************************/
  /*
  public function getDescriptorAttribute($value){
      return ucwords(strtolower($value));
  }
  */

  public function getActiveAttribute(){
    return $this->parseActiveAttribute();
  }

  private function parseActiveAttribute() {
    return ($this->opendate!='0000-00-00' && $this->closedate=='0000-00-00') ? 1 : 0;
  }


  public function getRouteKey()
{
    return $this->slug;
}
  
}
