<?php

function is_day($value){
	return  preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])$/", $value);
}

function is_month($value){
	return  preg_match("/^(0[1-9]|1[0-2])$/", $value);
}

function is_year($value){
	return preg_match('/(20[0-9][0-9])$/', $value);
}

function is_iso_date($date){
	return preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date);
}

function now($val=null){
	switch ($val) {
		case 'year':
			return date('Y', strtotime('now'));
			break;
		case 'month':
			return date('m', strtotime('now'));
			break;
		case 'day':
			return date('d', strtotime('now'));
			break;
		case 'Y':
			return date('Y', strtotime('now'));
			break;
		case 'M':
			return date('m', strtotime('now'));
			break;
		case 'D':
			return date('d', strtotime('now'));
			break;
		default:
			return date('Y-m-d', strtotime('now'));
			break;
	}
	
}

function pad($val, $len=2, $char='0', $direction=STR_PAD_LEFT){
	return str_pad($val, $len, $char, $direction);
}


function is_uuid($uuid=0) {
	return preg_match('/^[A-Fa-f0-9]{32}+$/', $uuid);
}


/**
 * Return sizes readable by humans
 */
function human_filesize($bytes, $decimals = 2)
{
  $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];
  $factor = floor((strlen($bytes) - 1) / 3);

  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) .
      @$size[$factor];
}

/**
 * Is the mime type an image
 */
function is_image($mimeType)
{
    return starts_with($mimeType, 'image/');
}


function endKey($array){
	end($array);
	return key($array);
}

function clientIP(){
	$ipAddress = $_SERVER['REMOTE_ADDR'];
	if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
    $ipAddress =  $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	return $ipAddress;
}




function lastWeekOfYear($year='') {
	$year = empty($year) ? date('Y', strtotime('now')):$year;
  $date = new \DateTime;
  $date->setISODate($year, 53);
  return ($date->format("W") === "53" ? 53 : 52);
}

function firstDayOfWeek($weekno='', $year=''){
	$weekno = empty($weekno) ? date('W', strtotime('now')) : $weekno;
	$year = empty($year) ? date('Y', strtotime('now')) : $year;
	$dt = new DateTime();
	$dt->setISODate($year, $weekno);
	return $dt;
}

function filename_to_date($filename, $type='l'){
	$f = pathinfo($filename, PATHINFO_FILENAME);

	$m = substr($f, 2, 2);
	$d = substr($f, 4, 2);
	$y = '20'.substr($f, 6, 2);

	if($type==='l')
		return $y.'-'.$m.'-'.$d;
	if($type==='s')
		return $m.'/'.$d.'/'.$y;
	return $y.'-'.$m.'-'.$d;
}

function filename_to_date2($filename){
	$f = pathinfo($filename, PATHINFO_FILENAME);

	$m = substr($f, 2, 2);
	$d = substr($f, 4, 2);
	$y = '20'.substr($f, 6, 2);

	return Carbon\Carbon::parse($y.'-'.$m.'-'.$d);
}


function vfpdate_to_carbon($f){
	

	$m = substr($f, 4, 2);
	$d = substr($f, 6, 2);
	$y = substr($f, 0, 4);

	return Carbon\Carbon::parse($y.'-'.$m.'-'.$d);
}


function carbonCheckorNow($date=NULL) {

	if(is_null($date))
		return Carbon\Carbon::now();

	try {
		$d = Carbon\Carbon::parse($date); 
	} catch(\Exception $e) {
		return Carbon\Carbon::now(); 
	}
	return $d;
}

function diffForHumans(Carbon\Carbon $time) {
  return str_replace(["after", "before"], "", Carbon\Carbon::now()->diffForHumans($time));
}


function logAction($action, $log, $logfile=NULL) {
	$logfile = !is_null($logfile) 
		? $logfile
		: base_path().DS.'logs'.DS.now().'-log.txt';

	$dir = pathinfo($logfile, PATHINFO_DIRNAME);

	if(!is_dir($dir))
		mkdir($dir, 0775, true);

	$new = file_exists($logfile) ? false : true;
	if($new){
		$handle = fopen($logfile, 'w+');
		chmod($logfile, 0775);
	} else
		$handle = fopen($logfile, 'a');

	$ip = clientIP();
	$brw = $_SERVER['HTTP_USER_AGENT'];
	$content = date('r')." | {$ip} | {$action} | {$log} \t {$brw}\n";
  fwrite($handle, $content);
  fclose($handle);
}	



if (!function_exists('c')) {
	function c($datetime=null) {
		return is_null($datetime) 
		? Carbon\Carbon::now()
		: Carbon\Carbon::parse($datetime);
	}
}

if (!function_exists('back_btn')) {
	function back_btn($url) {
		return empty(URL::previous())
		? $url
		: URL::previous();
	}
}

if (!function_exists('rand_color')) {
	function rand_color() {
    	return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
	}
}

if (!function_exists('is_me')) {
	function is_me($id=NULL) {
		$id = is_null($id) ? session('user.id') : $id;
    return '41F0FB56DFA811E69815D19988DDBE1E'==$id ? true : false;
	}
}

if (!function_exists('nice_format')) {
	function nice_format($n) {
	  // first strip any formatting;
	  $n = (0+str_replace(",","",$n));
	  
	  // is this a number?
	  if(!is_numeric($n)) return false;
	  
	  // now filter it;
	  if($n>1000000000000) return round(($n/1000000000000),1).'T';
	  else if($n>1000000000) return round(($n/1000000000),1).'B';
	  else if($n>1000000) return round(($n/1000000),1).'M';
	  else if($n>1000) return round(($n/1000),1).'K';
	  
	  return number_format($n,2);
	}
}

if (!function_exists('dayDesc')) {
  function dayDesc($x=1, $short=false) {
      
    switch ($x) {
    	case '0':
      	if ($short)
      		echo 'O';
      	else	
        	echo 'Day Off';
        break;
       case '1':
      	if ($short)
      		echo 'D';
      	else	
        	echo 'With Duty';
        break;
      case '2':
      	if ($short)
      		echo 'L';
      	else	
        	echo 'On Leave';
        break;
      case '3':
      	if ($short)
      		echo 'S';
      	else	
        	echo 'Suspended';
        break;
      case '4':
      	if ($short)
      		echo 'B';
      	else	
        	echo 'Backup';
        break;
      case '5':
      	if ($short)
      		echo 'R';
      	else	
        	echo 'Resigned';
        break;
      case '6':
      	if ($short)
      		echo 'X';
      	else	
        	echo 'Others';
        break;
      default:
        echo '-';
        break;
    }
                      
  }
}

if (!function_exists('contact_icon')) {
  function contact_icon($x, $full=false) {
  	switch ($x) {
    	case '2':
    		if ($full)
    			return '<span class="gly gly-phone-alt"></span>';
    		else
    			return 'gly-phone-alt';
    	case '3':
    		if ($full)
    			return '<span class="gly gly-fax"></span>';
    		else
    			return 'gly-fax';
    	default:
    		if ($full)
    			return '<span class="gly gly-iphone"></span>';
    		else
        	return 'gly-iphone';
        break;
    }
  }
}


if (!function_exists('jquery_mask')) {
  function jquery_mask($x, $full=false) {
  	switch ($x) {
    	case '2':
    		if ($full)
    			return '<span class="gly gly-phone-alt"></span>';
    		else
    			return 'data-mask="(00) 000 0000" maxlength="8"';
    	case '3':
    		if ($full)
    			return '<span class="gly gly-fax"></span>';
    		else
    			return 'maxlength="17"';
    	default:
    		if ($full)
    			return '<span class="gly gly-iphone"></span>';
    		else
        	return 'data-mask="0000 0000000" maxlength="12"';
        break;
    }
  }
}

if (!function_exists('nf')) {
  function nf($x='0.00', $d=2) {
    if ($x==0)
      return '';
    return number_format($x, $d);
  }
}

if (!function_exists('clean_number_format')) {
  function clean_number_format($x='0.00') {
  	return floatval(preg_replace('/[^\d.]/', '', $x));
  }
}

if (!function_exists('nav_caption')) {
  function nav_caption($x) {
  	return config('menu.main.navbar-left.masterfiles.dropdown.'.$x.'.caption');
  }
}

if (!function_exists('hr_nav_caption')) {
  function hr_nav_caption($x) {
  	return config('menu.main-hr.navbar-left.masterfiles.dropdown.'.$x.'.caption');
  }
}


if (!function_exists('up')) {
  function up($x) {
    return strtoupper($x);
  }
}

if (!function_exists('page_title')) {
  function page_title($x) {
  	return ucwords(str_replace('_', ' ', $x));
  }
}


if (!function_exists('emp_status')) {
  function emp_status($x) {
  	switch ($x) {
    	case '1':
    		return 'TRAINEE';
    	case '2':
    		return 'CONTRACTUAL';
    	case '3':
    		return 'REGULAR';
    	case '4':
    		return 'RESIGNED';
    	case '5':
    		return 'TERMINATED';
    	default:
    		return '';
        break;
    }
  }
}


if (!function_exists('emp_ratetype')) {
  function emp_ratetype($x) {
  	switch ($x) {
    	case '1':
    		return 'DAY';
    	case '2':
    		return 'MON';
    	default:
    		return '*';
        break;
    }
  }
}

if (!function_exists('check_educ')) {
  function check_educ($x) {
  	switch ($x) {
    	case '1':
    		return 'DAY';
    	case '2':
    		return 'MON';
    	default:
    		return '*';
        break;
    }
  }
}

if (!function_exists('check_civil_status')) {
  function check_civil_status($x) {
  	switch ($x) {
    	case '1':
    		return 'SINGLE';
    	case '2':
    		return 'MARRIED';
    	case '3':
    		return 'SEPARATED';
    	default:
    		return '*';
        break;
    }
  }
}

if (!function_exists('check_gender')) {
  function check_gender($x, $full=false) {
  	switch ($x) {
    	case '1':
    		if ($full)
    			return 'MALE';
    		else
    			return 'M';
    	case '2':
    		if ($full)
    			return 'FEMALE';
    		else
    			return 'F';
    	default:
    		if ($full)
    			return '';
    		else
        	return '';
        break;
    }
  }
}

if (!function_exists('emp_paytype')) {
  function emp_paytype($x) {
    switch ($x) {
      case '1':
        return 'WEEKLY';
      case '2':
        return 'SEMI-MONTHLY';
      case '3':
        return 'MONTHLY';
      default:
        return '';
        break;
    }
  }
}

if (!function_exists('emp_ratetype2')) {
  function emp_ratetype2($x) {
    switch ($x) {
      case '1':
        return 'DAILY';
      case '2':
        return 'MONTHLY';
      default:
        return '';
        break;
    }
  }
}

if (!function_exists('enye')) {
  function enye($x) {
    return str_replace('¥', 'Ñ', utf8_encode(trim($x)));
  }
}


if (!function_exists('eyne')) {
  function eyne($x) {
    return str_replace('Ñ', '¥', utf8_encode(trim($x)));
  }
}






