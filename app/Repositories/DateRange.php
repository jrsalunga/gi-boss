<?php namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Http\Request;

class DateRange {

  public $fr;
  public $to;
  public $current_date;


  public function __construct(Request $request, $now = null) {
  	$this->current_date = $this->carbonCheckorNow($request->input('date'));
  	$this->setDates($request);
  }

  private function checkDates(Request $request) {
  	
  	try {
			$this->fr = Carbon::parse($request->input('fr').' 00:00:00');
		} catch(\Exception $e) {
			$this->fr = Carbon::parse($this->current_date->year.'-'.$this->current_date->month.'-01 00:00:00');
		}

		try {
			$this->to = Carbon::parse($request->input('to').' 00:00:00');
		} catch(\Exception $e) {
			$this->to = Carbon::parse($this->current_date->year.'-'.$this->current_date->month.'-'.$this->current_date->daysInMonth.' 00:00:00');
		}
		// if to less than fr
		if($this->to->lt($this->fr))
			$this->to = Carbon::parse($this->fr->year.'-'.$this->fr->month.'-'.$this->fr->daysInMonth.' 00:00:00');
  }

  private function setDates(Request $request) {

  	if (is_null($request->input('fr')) && !is_null($request->input('to'))) {
			
			$this->fr = Carbon::parse(date('Y-m-01', strtotime($request->input('to'))).' 00:00:00');	
			$this->to = Carbon::parse(date('Y-m-d', strtotime($request->input('to'))).' 00:00:00');
		
		} else if (!is_null($request->input('fr')) && is_null($request->input('to'))){

			$this->fr = Carbon::parse(date('Y-m-d', strtotime($request->input('fr'))).' 00:00:00');	
			$this->to = Carbon::parse(date('Y-m-t', strtotime($request->input('fr'))).' 00:00:00');

		} else if(is_null($request->input('fr')) && is_null($request->input('to'))){
		
			$this->getCurrentNewOrCookie($request);
		
		} else {

			$this->checkDates($request);

		}
  }



  private function getCurrentNewOrCookie(Request $request){

		if (is_null($request->cookie('fr')))
      $this->fr = Carbon::parse($this->current_date->year.'-'.$this->current_date->month.'-01 00:00:00');
		else 
			$this->fr = Carbon::parse($request->cookie('fr'));
		
		if (is_null($request->cookie('to')))
      $this->to =  Carbon::parse($this->current_date->year.'-'.$this->current_date->month.'-'.$this->current_date->daysInMonth.' 00:00:00');
		else 
			$this->to = Carbon::parse($request->cookie('to'));
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


}