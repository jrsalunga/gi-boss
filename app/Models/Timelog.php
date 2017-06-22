<?php namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;

class Timelog extends BaseModel {

	protected $table = 'timelog';
 	protected $fillable = ['employeeid', 'rfid', 'branchid', 'datetime', 'txncode', 'entrytype', 'terminal', 'createdate'];
 	public static $header = ['code', 'lastname'];
 	protected $casts = [
    'txncode' => 'integer',
    'entrytype' => 'integer',
    'ignore' => 'integer'
  ];

  public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);
    if (app()->environment()==='production')
      $this->setConnection('mysql-tk');
    else  
    	$this->setConnection('tk-live');
  }

 	public function employee() {
    return $this->belongsTo('App\Models\Employee', 'employeeid');
  }

  public function branch() {
    return $this->belongsTo('App\Models\Branch', 'branchid');
  }








/***************** query scope *****************************************************/
	



/***************** mutators *****************************************************/
  public function getDatetimeAttribute($value){
    return Carbon::parse($value);
  }

  public function getCreatedateAttribute($value){
    return Carbon::parse($value);
  }


/***************** misc functions *****************************************************/
  public function getTxnCode($x=null){
  	$x = is_null($x) ? $this->txncode : $x;
  	switch ($x) {
			case 1:
				return 'Time In';
				break;
			case 2:
				return 'Break Start';
				break;
			case 3:
				return 'Break End';
				break;
			case 4:
				return 'Time Out';
				break;
			default:
				return '-';
				break;
		}
	}

	public function txnCode($x=null){
		$x = is_null($x) ? $this->txncode : $x;
  	switch ($x) {
			case 1:
				return 'TI';
				break;
			case 2:
				return 'BS';
				break;
			case 3:
				return 'BE';
				break;
			case 4:
				return 'TO';
				break;
			default:
				return '-';
				break;
		}
	}

	public function txnClass($x=null){
		$x = is_null($x) ? $this->txncode : $x;
  	switch ($x) {
			case 1:
				return 'success';
				break;
			case 2:
				return 'info';
				break;
			case 3:
				return 'warning';
				break;
			case 4:
				return 'danger';
				break;
			default:
				return '-';
				break;
		}
	}

	public function txnBgColor($x=null){
		$x = is_null($x) ? $this->txncode : $x;
  	switch ($x) {
			case 1:
				return '#dff0d8';
				break;
			case 2:
				return '#d9edf7';
				break;
			case 3:
				return '#fcf8e3';
				break;
			case 4:
				return '#f2dede';
				break;
			default:
				return '-';
				break;
		}
	}

	public function txnColor($x=null){
		$x = is_null($x) ? $this->txncode : $x;
  	switch ($x) {
			case 1:
				return '#3c763d';
				break;
			case 2:
				return '#31708f';
				break;
			case 3:
				return '#8a6d3b';
				break;
			case 4:
				return '#a94442';
				break;
			default:
				return '-';
				break;
		}
	}

	public function getEntry($x=null){
		$x = is_null($x) ? $this->entrytype : $x;
  	switch ($x) {
			case 1:
				return 'RFID';
				break;
			case 2:
				return 'Manual';
				break;
			default:
				return '-';
				break;
		}
	}


	public function entryCode($x=null){
		$x = is_null($x) ? $this->entrytype : $x;
  	switch ($x) {
			case 1:
				return 'ID';
				break;
			case 2:
				return 'M';
				break;
			default:
				return '-';
				break;
		}
	}

	public function entryClass($x=null){
		$x = is_null($x) ? $this->entrytype : $x;
  	switch ($x) {
			case 1:
				return 'primary';
				break;
			case 2:
				return 'danger';
				break;
			default:
				return '-';
				break;
		}
	}

	public function getStoreDate() {
		$start = Carbon::parse($this->datetime->format('Y-m-d').' 06:00:00');
		if ($this->datetime->gte($start))
			return $start;
    else
    	return $start->copy()->subDay();
	}

	/*********   http://laravel.com/docs/eloquent#query-scopes    *******************/
	
	public function scopeEmployeeid($query, $employeeid) {
		return $query->whereEmployeeid($employeeid);
  }

  public function scopeDate($query, $date) {
  	$date = $date instanceof Carbon ? $date->format('Y-m-d') : $date;
    return $query->where('datetime', 'like', $date.'%');
  }
	
	public function scopeTxncode($query, $txncode) {
		return $query->whereTxncode($txncode);
  }
	
	public function scopeEntrytype($query, $entrytype) {
		return $query->whereEntrytype($entrytype);
  }
	
	public function scopeTerminalid($query, $terminalid) { 	
		return $query->whereTerminalid($terminalid);
  }
  
}
