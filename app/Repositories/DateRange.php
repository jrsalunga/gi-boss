<?php namespace App\Repositories;

use DateInterval;
use DatePeriod;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DateRange {

  public $fr;
  public $to;
  public $date;
  public $now;
  protected $request;
  protected $modes = ['daily', 'month', 'monthly', 'weekly', 'quarterly', 'yearly'];
  protected $mode;
  public $diffInDays;


  public function __construct(Request $request, $now = null) {
    $this->date = $this->carbonCheckorNow($request->input('date'));
  	$this->request = $request;
  	$this->setDates($request);
  	$this->now = Carbon::now();
  }

  public function getMode() {
    return $this->mode;
  }

  public function setMode($mode=null) {
    return $this->mode = $mode;
  }

  public function getModes() {
    return $this->modes;
  }

  private function checkDates(Request $request) {
  	
  	try {
			$this->fr = Carbon::parse($request->input('fr').' 00:00:00');
		} catch(\Exception $e) {
			//$this->fr = Carbon::parse($this->date->year.'-'.$this->date->month.'-01 00:00:00');
			$this->fr = $this->date;
		}
		
		try {
			$this->to = Carbon::parse($request->input('to').' 23:59:59');
		} catch(\Exception $e) {
			//$this->to = Carbon::parse($this->date->year.'-'.$this->date->month.'-'.$this->date->daysInMonth.' 00:00:00');
			$this->to = $this->date;
		}
		
		// if to less than fr
		if($this->to->lt($this->fr))
			$this->to = Carbon::parse($this->fr->year.'-'.$this->fr->month.'-'.$this->fr->daysInMonth.' 00:00:00');
  	
  }

  public function setDates(Request $request) {

  	if (is_null($request->input('fr')) && !is_null($request->input('to'))) {
			
			$this->fr = Carbon::parse(date('Y-m-01', strtotime($request->input('to'))).' 00:00:00');	
			$this->to = Carbon::parse(date('Y-m-d', strtotime($request->input('to'))).' 00:00:00');
		
		} else if (!is_null($request->input('fr')) && is_null($request->input('to'))){

			$this->fr = Carbon::parse(date('Y-m-d', strtotime($request->input('fr'))).' 00:00:00');	
			$this->to = Carbon::parse(date('Y-m-t', strtotime($request->input('fr'))).' 00:00:00');

		} else if(!is_null($request->input('date'))) {

			$this->date = $this->carbonCheckorNow($request->input('date'));	
			$this->fr = $this->date;	
			$this->to = $this->date;

		} else if(is_null($request->input('fr')) && is_null($request->input('to')) && is_null($request->input('date'))){

			$this->getCurrentNewOrCookie($request);
		
		} else {

			$this->checkDates($request);

		}
  }



  private function getCurrentNewOrCookie(Request $request){

		if (is_null($request->cookie('fr')))
      $this->fr = Carbon::parse($this->date->year.'-'.$this->date->month.'-01 00:00:00');
		else 
			$this->fr = Carbon::parse($request->cookie('fr'));
		
		if (is_null($request->cookie('to')))
      $this->to =  Carbon::parse($this->date->year.'-'.$this->date->month.'-'.$this->date->daysInMonth.' 00:00:00');
		else 
			$this->to = Carbon::parse($request->cookie('to'));

		if (is_null($request->cookie('date')))
      $this->date =  Carbon::parse($this->date->year.'-'.$this->date->month.'-'.$this->date->day.' 00:00:00');
		else 
			$this->date = Carbon::parse($request->cookie('date'));
  }

  public function hourInterval() {
    $o = $this->fr->copy();
    $arr = [];
    do {
      array_push($arr, Carbon::parse($o->format('Y-m-d').' '.$o->format('H').':00:00'));
    } while ($o->addHour() <= $this->to->copy()->addHour());
   return $arr;
  }

  public function dateInterval(){
  	$to = $this->to->copy();
    $interval = new DateInterval('P1D');
    $to->add($interval);
    return new DatePeriod($this->fr, $interval, $to);
  }

  public function dateInterval2(){
    //$to = $this->to->copy();
    $to = $this->to->copy()->subDay();
    $interval = new DateInterval('P1D');
    $to->add($interval);
    return new DatePeriod($this->fr, $interval, $to);
  }

  public function monthInterval(){
  	$fr = $this->fr->copy();
  	$arr = [];
  	 do {
      array_push($arr, Carbon::parse($fr->format('Y-m-d')));
    } while ($fr->addMonth() <= $this->to);

    return $arr;
  }

  // leap year function
  public function monthInterval2(){ 
    $x =  0;
    $arr = [];
    $fr = $this->fr->copy();
     do {
      array_push($arr, Carbon::parse($fr->copy()->addMonths($x)->subDays(5)->lastOfMonth()->format('Y-m-d')));
      $x++;
    } while ($x <= $this->fr->copy()->startOfMonth()->diffInMonths($this->to));
    return $arr;
  }

  public function weekInterval(){
  	$fr = $this->fr->copy();
  	$arr = [];
  	 do {
      array_push($arr, Carbon::parse($fr->format('Y-m-d')));
    } while ($fr->addDays(7) <= $this->to);
    return $arr;
  }

  public function quarterInterval(){
  	$fr = $this->fr->copy();
  	$arr = [];
  	 do {
      array_push($arr, Carbon::parse($fr->format('Y-m-d')));
    } while ($fr->addMonths(3) <= $this->to);
    return $arr;
  }


  public function yearInterval(){
    $fr = $this->fr->copy()->lastOfYear();
    $arr = [];
     do {
      array_push($arr, Carbon::parse($fr->format('Y-m-d')));
    } while ($fr->addYear() <= $this->to);
    return $arr;
  }

  public function yearInterval2(){
    $x = 0;
    $arr = [];
    $fr = $this->fr->copy();
     do {
      array_push($arr, Carbon::parse($fr->copy()->addYears($x)->lastOfYear()->format('Y-m-d')));
      $x++;
    } while ($x <= ($this->to->format('Y')-$this->fr->format('Y')));
    return $arr;
  }




  public function carbonCheckorNow($date=NULL) {

		if(is_null($date))
			return Carbon::now();
		
		try {
			return Carbon::parse($date); 
		} catch(\Exception $e) {
			return Carbon::now(); 
		}
	}


  private function setDateRangeFromRequest(Request $request) {
    
    if($request->has('fr'))
      $this->fr = Carbon::parse($request->input('fr').' 00:00:00');
    elseif ($request->has('date'))
      $this->fr = Carbon::parse($request->input('date'))->startOfMonth();
    else
      $this->fr = Carbon::now()->startOfMonth();

    if($request->has('to'))
      $this->to = Carbon::parse($request->input('to').' 23:59:59');
    elseif ($request->has('date'))
      $this->to = Carbon::parse($request->input('date'))->endOfMonth();
    else
      $this->to = Carbon::now()->startOfDay();
    
    // if to less than fr
    if($this->to->lt($this->fr)) {
      $temp = $this->to;
      $this->to = $this->fr;
      $this->fr = $temp;
    }
    
  }


  // modify the date on DateRange instanced based on the 'mode'
  public function setDateRangeMode($arg=null, $arg2=null) { 

    if (is_null($arg) && is_null($arg)) {
      $request = $this->request;
      $mode = 'daily';
    } elseif (in_array($arg, $this->modes) && $arg2==null) {
      $request = $this->request;
      $mode = $arg;
    } elseif ($arg instanceof Request && in_array($arg2, $this->modes)) {
      $request = $arg;
      $mode = $arg2;
    }

    //$this->setDateRangeFromRequest($request);

    $this->mode = $mode;
    $y=false;
    switch ($mode) {
      case 'month':
        $this->to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->endOfMonth();
        $this->fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $this->to->copy()->startOfMonth();
        if ($this->to->lt($this->fr)) {
          $this->to = Carbon::now()->endOfMonth();
          $this->fr = $this->to->copy()->startOfMonth(); //$this->to->copy()->startOfMonth();
        } else {
          $this->to = $this->to->endOfMonth();
          $this->fr = $this->fr->startOfMonth();
        }
        $this->date = Carbon::now();
        break;
      
      case 'monthly':
        $this->to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->endOfMonth();
        $this->fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $this->to->copy()->subMonths(5)->startOfMonth();
        if ($this->to->lt($this->fr)) {
          $this->to = Carbon::now()->endOfMonth();
          $this->fr = $this->to->copy()->subMonths(5)->startOfMonth(); //$this->to->copy()->startOfMonth();
        } else {
          $this->to = $this->to->endOfMonth();
          $this->fr = $this->fr->startOfMonth();
        }
        break;
      case 'daily':
        $this->to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->endOfMonth();
        $this->fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $this->to->copy()->startOfMonth();
        if ($this->to->lt($this->fr)) {
          $this->to = Carbon::now();
          $this->fr = $this->to->copy()->startOfMonth();
        }
        break;
      case 'weekly':
        $this->to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->endOfWeek();
        $this->fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $this->to->copy()->subWeeks(5)->startOfWeek();
        if ($this->to->lt($this->fr)) {
          $this->to = Carbon::now()->endOfWeek();
          $this->fr = $this->to->copy()->subWeeks(5)->startOfWeek(); //$this->to->copy()->startOfWeek();
        } else {
          $this->to = $this->to->endOfWeek();
          $this->fr = $this->fr->startOfWeek();
        }
        break;
      case 'quarterly':
        $this->to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->lastOfQuarter();
        $this->fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $this->to->copy()->subMonths(11)->firstOfQuarter();
        if ($this->to->lt($this->fr)) {
          $this->to = Carbon::now()->lastOfQuarter();
          $this->fr = $this->to->copy()->subMonths(12)->firstOfQuarter(); //$this->to->copy()->startOfWeek();
        } else {
          $this->to = $this->to->lastOfQuarter();
          $this->fr = $this->fr->firstOfQuarter();
        }
        break;
      case 'yearly':
        $y=true;
        $this->to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->lastOfYear();
        $this->fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $this->to->copy()->subYear()->firstOfYear();
        if ($this->to->lt($this->fr)) {
          $this->to = Carbon::now()->lastOfYear();
          $this->fr = $this->to->copy()->subYear()->firstOfYear(); //$this->to->copy()->startOfWeek();
        } else {
          $this->to = $this->to->lastOfYear();
          $this->fr = $this->fr->firstOfYear();
        }
        break;
      default:
        $this->to = Carbon::now()->endOfDay();
        $this->fr = $this->to->copy()->startOfMonth();
        break;
    }
    

    if(!$y){
      
      // if more than a year
      if($this->fr->diffInDays($this->to, false)>=731) { // 730 = 2yrs
        $this->fr = $this->to->copy()->subDays(730)->startOfMonth();
        $this->to = $this->to;
        $this->date = $this->to;
        return false;
      }
    }


   
    return true;
  }

  public function diffInDays() {
    return $this->diffInDays = $this->fr->diffInDays($this->to, false);
  }


}