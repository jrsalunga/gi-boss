<?php namespace App\Models;

use App\Models\BaseModel;

class Employee extends BaseModel {

  //protected $connection = 'hr';
	protected $table = 'hr.employee';
  //protected $fillable = ['code', 'lastname', 'firstname', 'middlename', 'companyid', 'positionid', 'branchid', 'punching', 'processing'];
  protected $guarded = ['id'];
 	public static $header = ['code', 'lastname'];
  protected $dates = ['datehired', 'datestart', 'datestop', 'birthdate'];
  public $timestamps = false;

  
  public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);
    if (app()->environment()==='production')
      $this->setConnection('mysql-hr');
    else  
      $this->setConnection('hr-live');
  }



  public function department() {
    return $this->belongsTo('App\Models\Department', 'deptid');
  }
  

 	public function timelogs() {
    return $this->hasMany('App\Models\Timelog', 'employeeid');
  }

  public function branch() {
    return $this->belongsTo('App\Models\Branch', 'branchid');
  }

  public function company() {
    return $this->belongsTo('App\Models\Company', 'companyid');
  }

  public function position() {
    return $this->belongsTo('App\Models\Position', 'positionid');
  }

  public function uploads() {
    return $this->hasMany('App\Models\Upload', 'employeeid');
  }

  public function manskeddtls() {
    return $this->hasMany('App\Models\Manskeddtl', 'employeeid');
  }

  public function manskedhdr() {
    return $this->hasMany('App\Models\Manskedhdr', 'managerid');
  }

  public function childrens() {
    return $this->hasMany('App\Models\Children', 'employeeid');
  }

  public function ecperson() {
    return $this->hasOne('App\Models\Ecperson', 'employeeid');
  }

  public function educations() {
    return $this->hasMany('App\Models\Education', 'employeeid');
  }

  public function workexps() {
    return $this->hasMany('App\Models\Workexp', 'employeeid');
  }

  public function spouse() {
    return $this->hasOne('App\Models\Spouse', 'employeeid');
  }

  public function religion() {
    return $this->belongsTo('App\Models\Religion', 'religionid');
  }

  public function statutory() {
    return $this->hasOne('App\Models\Statutory');
  }


  public function empfile() {
    return $this->hasOne('App\Models\Empfile');
  }




   /**
     * Query Scope.
     *
     */
   // Employee::Branchid('1')->get()
    public function scopeBranchid($query, $id)
    {
        return $query->where('branchid', $id);
    }

  public function scopeProcessing($query, $x='1'){
    return $query->where('processing', $x);
  }



  /***************** mutators *****************************************************/
  public function _getLastnameAttribute($value){
    return mb_convert_case($value, MB_CASE_TITLE, "UTF-8");
    //return ucwords(strtolower($value));
  }

  public function _getFirstnameAttribute($value){
    return mb_convert_case($value, MB_CASE_TITLE, "UTF-8");
    //return ucwords(strtolower($value));
  }

  public function _getMiddlenameAttribute($value){
    return mb_convert_case($value, MB_CASE_TITLE, "UTF-8");
    //return ucwords(strtolower($value));
  }

  public function getPhotoAttribute(){
    return file_exists('../../gi-cashier/public/images/employees/'.$this->code.'.jpg');
  }

  public function getPhotoUrl(){
    return $this->photo
      ? 'http://cashier.giligansrestaurant.com/images/employees/'.$this->code.'.jpg'
      : 'http://cashier.giligansrestaurant.com/images/login-avatar.png';
  }

  public function getBirthdate() {
    return is_iso_date($this->birthdate->format('Y-m-d'))
      ? $this->birthdate->format('Y-m-d')
      : NULL;
  }

  public function getDatestart() {
    return is_iso_date($this->datestart->format('Y-m-d'))
      ? $this->datestart->format('Y-m-d')
      : NULL;
  }

  public function isConfirm() {

    //$this->load('empfile');
    if (!is_null($this->empfile))
      return true;
    return false;
  }

  public function hasEmpfile($type='MAS') {

    switch ($type) {
      case 'MAS':
        return file_exists(config('giligans.path.files.'.app()->environment()).'EMPFILE'.DS.$type.DS.$this->code.'.'.$type);
        break;
      case 'EMP':
        return file_exists(config('giligans.path.files.'.app()->environment()).'EMPFILE'.DS.$type.DS.$this->code.'.'.$type);
        break;
      default:
        return false;
        break;
    }
  }




  public function sssno() {
    if (empty($this->sssno))
      return NULL;

    $s = str_replace('-', '', $this->sssno);
    return substr($s, -10, 2).'-'.substr($s, -8, 7).'-'.substr($s, -1, 1);
  }

  public function hdmfno() {
    if (empty($this->hdmfno))
      return NULL; 

    $s = str_replace('-', '', $this->hdmfno);
    return substr($s, -12, 4).'-'.substr($s, -8, 4).'-'.substr($s, -4, 4); 
  }

  public function tin() {
    if (empty($this->tin))
      return NULL;
    
    $s = str_replace('-', '', $this->tin);
    return substr($s, 0, 3).'-'.substr($s, 3, 3).'-'.substr($s, 6, 3).'-'.str_pad(substr($s, 9, 3), 3, '0'); 
  }

  public function isActive() {
    return ($this->empstatus==4 || $this->empstatus==5 || is_iso_date($this->datestop->format('Y-m-d')) || empty($this->branchid))
      ? false
      : true;
  }


  public function height() {
    return ($this->height+0);
  }

  public function weight() {
    return ($this->weight+0);
  }

  
	
}
