<?php namespace App\Http\Controllers;

use Exception;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\DailySalesRepository as DSRepo;
use App\Repositories\BossBranchRepository as BBRepo;
use App\Repositories\Criterias\BossBranchCriteria;
use App\Repositories\BranchRepository;
use App\Repositories\Criterias\ByBranchCriteria;
use App\Repositories\Criterias\BranchCriteria;

class AnalyticsController extends Controller
{
  protected $ds;
  protected $dr;
  protected $bb;

  public function __construct(DSRepo $dsrepo, BBRepo $bbrepo, DateRange $dr) {
    $this->ds = $dsrepo;
    $this->dr = $dr;
    $this->bb = $bbrepo;
    $this->bb->pushCriteria(new BossBranchCriteria);
    $this->branch = new BranchRepository;
  }


  public function getDaysByWeekNo($weekno='', $year=''){
    $weekno = (empty($weekno) || $weekno > 53) ? date('W', strtotime('now')) : $weekno;
    $year = empty($year) ?  date('Y', strtotime('now')) : $year;
        for($day=1; $day<=7; $day++) {
            $arr[$day-1] = Carbon::parse(date('Y-m-d', strtotime($year."W".$this->weekNo($weekno).$day)));
        }
      return $arr;
  }

  public function underConstruction(Request $request) {
    return 'Under Construction';
    return $request->all();
  }


  public function getWeekly(Request $request) {
    $bb = $this->bossBranch();
    
    // get /status/branch
    if(is_null($request->input('branchid'))) {
      return $this->setDailyViewVars('analytics.weekly', null, $bb, null);
    } 
    
    if(!is_uuid($request->input('branchid'))
    || !in_array(strtoupper($request->input('branchid')),  $this->branch->all()->pluck('id')->all())) 
    {
      return redirect('/status/branch/week')->with('alert-warning', 'Please select a branch.');
      //return $this->setDailyViewVars('status.branch', null, $bb, null)->withError('dadada');
    } 

    //return 'success';

    $res = $this->setDateRangeMode($request, 'weekly');

    try {
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    } catch (Exception $e) {
      return $this->setDailyViewVars('analytics.weekly', null, $bb, null);
    }

    $dailysales = $this->ds
                      ->pushCriteria(new BranchCriteria($branch))
                      ->getWeek($request, $this->dr);

    if(!$res)
      $request->session()->flash('alert-warning', 'Max months reached! Adjusted to '.$this->dr->fr->format('M Y').' - '.$this->dr->to->format('M Y'));

    return $this->setDailyViewVars('analytics.weekly', $dailysales, $bb,  $branch);
  }


  private function bossBranch(){
    return array_sort($this->branch->active()->all(['code', 'descriptor', 'id']), 
      function ($value) {
        return $value['code'];
    });
  }

  private function setDailyViewVars($view, $dailysales=null, $branches=null, $branch=null) {

    return $this->setViewWithDR(view($view)
                ->with('dailysales', $dailysales)
                ->with('branches', $branches)
                ->with('branch', $branch));
  }

  public function getDaily(Request $request) {



    /*
    return date('W, M d D', strtotime('2016-12-28'));
    $date = Carbon::parse(date('Y-m-d', strtotime("2015W130")));
    return $date;

    $date = Carbon::parse('2015-03-30');
    return $date->weekOfYear;
    */

    $bb = $this->bossBranch();

    // get /status/branch
    if(is_null($request->input('branchid'))) {
      return $this->setDailyViewVars('analytics.branch', null, $bb, null);
    } 
    
    if(!is_uuid($request->input('branchid'))
    || !in_array(strtoupper($request->input('branchid')), $this->branch->all()->pluck('id')->all())) 
    {
      return redirect('/status/branch')->with('alert-warning', 'Please select a branch.');
      //return $this->setDailyViewVars('status.branch', null, $bb, null)->withError('dadada');
    } 

    $res = $this->setDateRangeMode($request, 'daily');
    
    try {
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    } catch (Exception $e) {
      return $this->setDailyViewVars('analytics.branch', null, $bb, null);
    }

    $dailysales = $this->ds->branchByDR($branch, $this->dr);
    return $this->setDailyViewVars('analytics.branch', $dailysales, $bb,  $branch);

  }




  public function getMonth(Request $request) {
    $bb = $this->bossBranch();

    // get /status/branch
    if(is_null($request->input('branchid'))) {
      return $this->setDailyViewVars('analytics.month', null, $bb, null);
    } 
    
    if(!is_uuid($request->input('branchid'))
    || !in_array(strtoupper($request->input('branchid')),  $this->branch->all()->pluck('id')->all())) 
    {
      return redirect('/status/branch/month')->with('alert-warning', 'Please select a branch.');
      //return $this->setDailyViewVars('status.branch', null, $bb, null)->withError('dadada');
    } 

    //return 'success';

    $res = $this->setDateRangeMode($request, 'month');

    try {
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    } catch (Exception $e) {
      return $this->setDailyViewVars('analytics.month', null, $bb, null);
    }

    $dailysales = $this->ds
                      ->pushCriteria(new BranchCriteria($branch))
                      ->getMonth($request, $this->dr);

    if(!$res)
      $request->session()->flash('alert-warning', 'Max months reached! Adjusted to '.$this->dr->fr->format('M Y').' - '.$this->dr->to->format('M Y'));

    return $this->setDailyViewVars('analytics.month', $dailysales, $bb,  $branch);
      

  }





  // modify the date on DateRange instanced based on the 'mode'
  private function setDateRangeMode(Request $request, $mode='day') { 

    switch ($mode) {
      case 'month':
        $to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->endOfMonth();
        $fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $to->copy()->subMonths(5)->startOfMonth();
        if ($to->lt($fr)) {
          $to = Carbon::now()->endOfMonth();
          $fr = $to->copy()->startOfMonth();
        } else {
          $to = $to->endOfMonth();
          $fr = $fr->startOfMonth();
        }
        break;
      case 'daily':
        $to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->endOfMonth();
        $fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $to->copy()->startOfMonth();
        if ($to->lt($fr)) {
          $to = Carbon::now();
          $fr = $to->copy()->startOfMonth();
        }
        break;
      case 'weekly':
        $to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->endOfWeek();
        $fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $to->copy()->subWeeks(5)->startOfWeek();
        if ($to->lt($fr)) {
          $to = Carbon::now()->endOfWeek();
          $fr = $to->copy()->startOfWeek();
        } else {
          $to = $to->endOfWeek();
          $fr = $fr->startOfWeek();
        }
        break;
      default:
        $to = Carbon::now()->endOfMonth();
        $fr = $to->copy()->startOfMonth();
        break;
    }

    // if more than a year
    if($fr->diffInDays($to, false)>=731) { // 730 = 2yrs
      $this->dr->fr = $to->copy()->subDays(730)->startOfMonth();
      $this->dr->to = $to;
      $this->dr->date = $to;
      return false;
    }

    $this->dr->fr = $fr;
    $this->dr->to = $to;
    $this->dr->date = $to;
    return true;
  }






  private function setViewWithDR($view){
    $response = new Response($view->with('dr', $this->dr));
    $response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
    $response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
    $response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
    return $response;
  }
}
