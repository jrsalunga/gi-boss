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
use App\Repositories\ComponentRepository as CompRepo;
use App\Repositories\ProductRepository as ProdRepo;
use App\Repositories\Purchase2Repository as PurchRepo;
use App\Repositories\SalesmtdRepository as SalesRepo;

class AnalyticsController extends Controller
{
  protected $ds;
  protected $dr;
  protected $bb;
  protected $compRepo;
  protected $prodRepo;
  protected $purchRepo;
  protected $salesRepo;

  public function __construct(DSRepo $dsrepo, BBRepo $bbrepo, DateRange $dr, CompRepo $compRepo, ProdRepo $prodRepo, PurchRepo $purchRepo, SalesRepo $salesRepo) {
    $this->ds = $dsrepo;
    $this->dr = $dr;
    $this->bb = $bbrepo;
    $this->bb->pushCriteria(new BossBranchCriteria);
    $this->branch = new BranchRepository;
    $this->compRepo = $compRepo;
    $this->prodRepo = $prodRepo;
    $this->purchRepo = $purchRepo;
    $this->salesRepo = $salesRepo;
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
    $res = $this->setDateRangeMode($request, 'daily');

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

    
    try {
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    } catch (Exception $e) {
      return $this->setDailyViewVars('analytics.branch', null, $bb, null);
    }

    $dailysales = $this->ds->branchByDR($branch, $this->dr);

    if($request->has('export') && $request->input('export')==='true') {
      $ar = $this->dailyToArray($dailysales);
      return $this->exportDaily($ar);
    }

    if (!in_array($request->user()->id, ['41F0FB56DFA811E69815D19988DDBE1E', '11E943EA14DDA9E4EAAFBD26C5429A67'])) {

      $email = ['body' => $request->user()->name.' '.$branch->code.' '.$this->dr->fr->format('Y-m-d').' '.$this->dr->to->format('Y-m-d')];

      \Mail::queue('emails.notifier', $email, function ($m) {
        $m->from('giligans.app@gmail.com', 'GI App - Boss');
        $m->to('giligans.log@gmail.com')->subject('Daily Branch Analytics - '.rand());
      });
    }

    return $this->setDailyViewVars('analytics.branch', $dailysales, $bb,  $branch);
  }

  public function dailyToArray($ds) {
    $arr = [];

    array_push($arr, 
      ['Date', 'Sales', 'Food Cost', 'Purchased', 'Customers', 'Head Spead', 'Trans Count', 'Sales/Receipt', 'Emp Count', 'Sales/Emp', 'Mancost', 'Mancost %', 'Tips', 'Tips %']);

    return $arr;

  }

  public function exportDaily(array $data, $filename='download') {

    
    return \Excel::create($filename, function($excel) use ($data) {

      $excel->sheet('Sheetname', function($sheet) use ($data) {

        $sheet->fromArray($data);
      });

    })->export('csv');
  }


  public function getWeekly(Request $request) {
    $bb = $this->bossBranch();
    $res = $this->setDateRangeMode($request, 'weekly');
    
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

  public function getMonth(Request $request) {
    $bb = $this->bossBranch();
    $res = $this->setDateRangeMode($request, 'monthly');

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

    //return dd($this->dr);


    try {
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    } catch (Exception $e) {
      return $this->setDailyViewVars('analytics.month', null, $bb, null);
    }

    $dailysales = $this->ds
                      ->skipCache()
                      ->pushCriteria(new BranchCriteria($branch))
                      ->getMonth($request, $this->dr);

    if (!in_array($request->user()->id, ['41F0FB56DFA811E69815D19988DDBE1E', '11E943EA14DDA9E4EAAFBD26C5429A67'])) {

      $email = [
        'body' => $request->user()->name.' '.$branch->code.' '.$this->dr->fr->format('Y-m-d').' '.$this->dr->to->format('Y-m-d')
      ];

      \Mail::queue('emails.notifier', $email, function ($m) {
        $m->from('giligans.app@gmail.com', 'GI App - Boss');
        $m->to('freakyash_02@yahoo.com')->subject('Month Branch Analytics - '.rand());
      });
    }

    if(!$res)
      $request->session()->flash('alert-warning', 'Max months reached! Adjusted to '.$this->dr->fr->format('M Y').' - '.$this->dr->to->format('M Y'));

    return $this->setDailyViewVars('analytics.month', $dailysales, $bb,  $branch);
      

  }


  public function getQuarter(Request $request) {
    $bb = $this->bossBranch();
    $res = $this->setDateRangeMode($request, 'quarterly');

    // get /status/branch
    if(is_null($request->input('branchid'))) {
      return $this->setDailyViewVars('analytics.quarter', null, $bb, null);
    } 
    
    if(!is_uuid($request->input('branchid'))
    || !in_array(strtoupper($request->input('branchid')),  $this->branch->all()->pluck('id')->all())) 
    {
      return redirect('/status/branch/quarter')->with('alert-warning', 'Please select a branch.');
      //return $this->setDailyViewVars('status.branch', null, $bb, null)->withError('dadada');
    } 

    //return 'success';


    try {
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    } catch (Exception $e) {
      return $this->setDailyViewVars('analytics.quarter', null, $bb, null);
    }

    $dailysales = $this->ds
                      ->pushCriteria(new BranchCriteria($branch))
                      ->getQuarter($request, $this->dr);

    if(!$res)
      $request->session()->flash('alert-warning', 'Max months reached! Adjusted to '.$this->dr->fr->format('M Y').' - '.$this->dr->to->format('M Y'));

    return $this->setDailyViewVars('analytics.quarter', $dailysales, $bb,  $branch);
  }


  public function getYear(Request $request) {

    //return $request->all();

    $bb = $this->bossBranch();
    $res = $this->setDateRangeMode($request, 'yearly');

    //return dd($this->dr);
    // get /status/branch
    if(is_null($request->input('branchid'))) {
      return $this->setDailyViewVars('analytics.year', null, $bb, null);
    } 
    
    if(!is_uuid($request->input('branchid'))
    || !in_array(strtoupper($request->input('branchid')),  $this->branch->all()->pluck('id')->all())) 
    {
      return redirect('/status/branch/year')->with('alert-warning', 'Please select a branch.');
      //return $this->setDailyViewVars('status.branch', null, $bb, null)->withError('dadada');
    } 

    try {
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    } catch (Exception $e) {
      return $this->setDailyViewVars('analytics.year', null, $bb, null);
    }

    $dailysales = $this->ds
                      // ->skipCache()
                      ->pushCriteria(new BranchCriteria($branch))
                      ->getYear($request, $this->dr);
    if(!$res)
      $request->session()->flash('alert-warning', 'Max months reached! Adjusted to '.$this->dr->fr->format('M Y').' - '.$this->dr->to->format('M Y'));

    return $this->setDailyViewVars('analytics.year', $dailysales, $bb,  $branch);
  }





  // modify the date on DateRange instanced based on the 'mode'
  private function setDateRangeMode(Request $request, $mode='day') { 
    $y=false;
    switch ($mode) {
      case 'monthly':
        $to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->endOfMonth();
        $fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $to->copy()->subMonths(5)->startOfMonth();
        if ($to->lt($fr)) {
          $to = Carbon::now()->endOfMonth();
          $fr = $to->copy()->subMonths(5)->startOfMonth(); //$to->copy()->startOfMonth();
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
          $fr = $to->copy()->subWeeks(5)->startOfWeek(); //$to->copy()->startOfWeek();
        } else {
          $to = $to->endOfWeek();
          $fr = $fr->startOfWeek();
        }
        break;
      case 'quarterly':
        $to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->lastOfQuarter();
        $fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $to->copy()->subMonths(11)->firstOfQuarter();
        if ($to->lt($fr)) {
          $to = Carbon::now()->lastOfQuarter();
          $fr = $to->copy()->subMonths(12)->firstOfQuarter(); //$to->copy()->startOfWeek();
        } else {
          $to = $to->lastOfQuarter();
          $fr = $fr->firstOfQuarter();
        }
        break;
      case 'yearly':
        $y=true;
        $to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->lastOfYear();
        $fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $to->copy()->subYear()->firstOfYear();
        if ($to->lt($fr)) {
          $to = Carbon::now()->lastOfYear();
          $fr = $to->copy()->subYear()->firstOfYear(); //$to->copy()->startOfWeek();
        } else {
          $to = $to->lastOfYear();
          $fr = $fr->firstOfYear();
        }
        break;
      default:
        $to = Carbon::now()->endOfMonth();
        $fr = $to->copy()->startOfMonth();
        break;
    }
    

    if(!$y){
      
      // if more than a year
      if($fr->diffInDays($to, false)>=3650) { // 730 = 2yrs , 1825 = 5yrs, 3650 = 10yrs
        $this->dr->fr = $to->copy()->subDays(3650)->startOfMonth();
        $this->dr->to = $to;
        $this->dr->date = $to;
        return false;
      }
    }

    $this->dr->setMode($mode);
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


  public function getCompPurch(Request $request) {


    $where = [];
    $bb = $this->bossBranch();
    $d = ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'];
    $res = $this->setDateRangeMode($request, in_array($request->input('mode'),$d) ? $request->input('mode') : 'daily');

    $comps = $this->compRepo
              //->skipCache()
              ->scopeQuery(function($query){
                return $query->orderBy('descriptor');
              })
              ->all(['code', 'descriptor', 'id']);

    $prods = $this->prodRepo
              //->skipCache()
              ->scopeQuery(function($query){
                return $query->whereNotIn('prodcat_id', ['E841F22BBC3711E6856EC3CDBB4216A7'])
                          ->where('descriptor', '<>', '')
                          ->orderBy('descriptor');
              })
              ->all(['code', 'descriptor', 'id']);
    

    $component = null;
    if ($request->has('componentid') && is_uuid($request->input('componentid'))) {
      try {
        $component = $this->compRepo->find($request->input('componentid'), ['code', 'descriptor', 'id']);
      } catch (Exception $e) {
        $component = null;
      }
    }

    $product = null;
    if ($request->has('productid') && is_uuid($request->input('productid'))) {
      try {
        $product = $this->prodRepo->find($request->input('productid'), ['code', 'descriptor', 'id']);
      } catch (Exception $e) {
        $product = null;
      }
    }


    if(is_null($request->input('branchid'))) 
      return $this->setViewWithDR(view('layouts.br-dr')
        ->with('branches', $bb)
        ->with('branch', null)
        ->with('datas', null)
        ->with('comps', $comps)
        ->with('prods', $prods)
        ->with('includes', 'layouts.ldr'));
    


    try {
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    } catch (Exception $e) {
      return $this->setViewWithDR(view('layouts.br-dr')
        ->with('branches', $bb)
        ->with('branch', null)
        ->with('datas', null)
        ->with('comps', $comps)
        ->with('prods', $prods)
        ->with('includes', 'layouts.ldr'));
    }

    $datas = [];
    
    $purchases = null;
    if (!is_null($component)) {
      $purchases = $this->purchRepo
                      ->branchGroupByDr($branch, $this->dr)
                      ->findWhere(['branchid' => $branch->id, 'componentid'=>$component->id], ['date', 'qty', 'tcost']);
    }

    $sales = null;
    if (!is_null($product)) {
      $sales = $this->salesRepo
            ->skipCache()
            ->groupByDateDr($this->dr)
            ->findWhere(['branch_id'=>$branch->id, 'product_id'=>$product->id], ['orddate', 'ordtime', 'grsamt', 'qty']);
    }

    //return $request->all();
    //return $sales;

    foreach ($this->dr->dateInterval() as $key => $date) {
      
      $datas[$date->format('Ymd')]['date'] = $date;
      
      if ($sales) {
        $datas[$date->format('Ymd')]['product'] = $sales->filter(function ($item) use ($date) {
          if ($item->orddate->format('Y-m-d') == $date->format('Y-m-d')) 
              return $item;
        })->first();
      } else {
        $datas[$date->format('Ymd')]['product'] = null;
      }

      if ($purchases) {
        $datas[$date->format('Ymd')]['component'] = $purchases->filter(function ($item) use ($date) {
          if ($item->date->format('Y-m-d') == $date->format('Y-m-d')) 
              return $item;
        })->first();
      } else {
        $datas[$date->format('Ymd')]['component'] = null;
      }
    }


    //return $datas;






    


    return $this->setViewWithDR(view('layouts.br-dr')
      ->with('datas', $datas)
      ->with('branches', $bb)
      ->with('branch', $branch)
      ->with('includes', 'layouts.ldr')
      ->with('component', $component)
      ->with('product', $product)
      ->with('comps', $comps)
      ->with('prods', $prods));
  }
}
