<?php namespace App\Repositories;
use DB;
use StdClass;
use Carbon\Carbon;
use App\Models\DailySales;
use App\Models\Branch;
use App\Repositories\DateRange;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\BossBranchRepository as BBRepo;
use App\Repositories\Criterias\BossBranchCriteria;
use App\Repositories\Criterias\ActiveBranchCriteria as ActiveBranch;
use App\Repositories\Criterias\BranchDailySalesCriteria;

use Illuminate\Http\Request;

use App\Repositories\BranchRepository;

use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;

class DailySalesRepository extends BaseRepository implements CacheableInterface {
//class DailySalesRepository extends BaseRepository {

  use CacheableRepository;
	
	public $bossbranch;

	public function __construct(BBRepo $bbrepo, DailySales $dailysales, BranchRepository $branch) {
		parent::__construct(app());
		$this->bossbranch = $bbrepo;
		$this->bossbranch->pushCriteria(new BossBranchCriteria);
    $this->branch = new BranchRepository;
    $this->branch->pushCriteria(new ActiveBranch);
	}
	





  /**
   * Specify Model class name
   *
   * @return string
   */
  function model()
  {
      return "App\\Models\\DailySales";
  }

  /**
   * starred braches by date //dailysales
   *
   */
  public function branchByDate(Carbon $date) {
  	$ads = []; // array of dailysales
  	$bb = $this->bossbranch->with([
  			'branch'=>function($q){
  				$q->select(['code', 'descriptor', 'mancost', 'id']);
  			}
  		])->all();

  	foreach ($bb as $b) { // each bossbranch
  		$ds = DailySales::whereBranchid($b->branchid)
  											->where('date', $date->format('Y-m-d'))
  											->first();
  		if(is_null($ds))
	  		$ads[$b->branch->code]['ds'] = NULL;

  		$ads[$b->branch->code]['ds'] = $ds;
  		$ads[$b->branch->code]['br'] = $b->branch;
   	}

   	return array_sort_recursive($ads);
  	return array_sort($ads, function($value){
  		return $value;
  	});
  }

  /**
   * all braches by date //dailysales/all
   */
  public function allBranchByDate(Carbon $date) {
    $ads = []; // array of dailysales
    //$bb = Branch::orderBy('code', 'ASC')->get();
    $bb = $this->branch->all(['code', 'descriptor', 'mancost', 'id']);
    //return $bb = $this->branch->getByCriteria(new ActiveBranch)->all(['code', 'descriptor', 'mancost', 'id']);

    foreach ($bb as $b) { // each bossbranch
      $ds = DailySales::whereBranchid($b->id)
                        ->where('date', $date->format('Y-m-d'))
                        ->first();
      if(is_null($ds))
        $ads[$b->code]['ds'] = NULL;

      $ads[$b->code]['ds'] = $ds;
      $ads[$b->code]['br'] = $b;
    }

    return array_sort_recursive($ads);
    return array_sort($ads, function($value){
      return $value;
    });
  }

  public function getSign($x=0) {

        if ($x > 0)
          return '+';
        else if ($x < 0)
          return '-';
        else
          return '';

      }

  // column 1 //dashboard
  public function todayTopSales(Carbon $date, $limit=10) {

    $arr = [];
    $ds_null = false;
    $current_day_zero_sales = false;

    $ds = DailySales::where('date', $date->format('Y-m-d'))->orderBy('sales', 'DESC')->take($limit)->get();
    
    

    if(count($ds)=='0') {
      $ds = DailySales::where('date', $date->copy()->subDay()->format('Y-m-d'))->orderBy('sales', 'DESC')->take($limit)->get();
      $ds_null = true;
    } else {
      foreach ($ds as $d) {
        if($d->sales == '0.00'){
          $ds = DailySales::where('date', $date->copy()->subDay()->format('Y-m-d'))->orderBy('sales', 'DESC')->take($limit)->get();
          $ds_null = true;
          continue;
        }
      }
    }

    foreach ($ds as $d) {
      $branch = Branch::where('id', $d->branchid)->get(['code', 'descriptor', 'id'])->first();
      
      if($ds_null) {
        $ds_today = new DailySales;
        $ds_yesteday = $d;
      } else {
        $ds_today = $d;
        $ds_yesteday = DailySales::where('date', $date->copy()->subDay()->format('Y-m-d'))->where('branchid', $d->branchid)->first(); 
      } 
      $ds_otherday = DailySales::where('date', $date->copy()->subDay(2)->format('Y-m-d'))->where('branchid', $d->branchid)->first(); 

      $s = new StdClass;
      $c = new StdClass;

      

      $s->branch = $branch;
      $s->today = $ds_today;
      $s->yesterday = $ds_yesteday;
      $s->otherday = $ds_otherday;

        $c->sales = ($ds_today->sales - $ds_yesteday->sales);
        $s->today->sign = $this->getSign($ds_today->sales - $ds_yesteday->sales);
        
        $c->sales1 = ($ds_yesteday->sales - $ds_otherday->sales);
        $s->yesterday->sign = $this->getSign($ds_yesteday->sales - $ds_otherday->sales);
      
      $s->diff = $c;

      array_push($arr, $s);
    }

    return collect($arr);
  }

  public function branchByDR(Branch $branch, DateRange $dr, $order = 'ASC') {
    
    $arr = [];
    $dss = $this->scopeQuery(function($query) use ($order, $dr) {
              return $query->whereBetween('date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                            ->orderBy('date', $order);
          })->findWhere([
            'branchid' => $branch->id
          ]);
    

    foreach ($dr->dateInterval() as $key => $date) {
      $filtered = $dss->filter(function ($item) use ($date){
          return $item->date->format('Y-m-d') == $date->format('Y-m-d')
                ? $item : null;
      });
      $obj = new StdClass;
      $obj->date = $date;
      $obj->dailysale = $filtered->first();
      $arr[$key] = $obj;
    }

    return collect($arr);

  }
  
  private function getAggregateByDateRange($fr, $to) {

    $sql = 'date, MONTH(date) AS month, YEAR(date) as year, SUM(sales) AS sales, SUM(slsmtd_totgrs) AS slsmtd_totgrs, ';
    $sql .= 'SUM(purchcost) AS purchcost, SUM(cos) AS cos, SUM(tips) AS tips, ';
    $sql .= 'SUM(custcount) AS custcount, SUM(empcount) AS empcount, SUM(headspend) AS headspend, branchid';

    return $this->scopeQuery(function($query) use ($fr, $to, $sql) {
      return $query->select(DB::raw($sql))
        ->whereBetween('date', [$fr, $to])
        ->groupBy(DB::raw('MONTH(date), YEAR (date)'))
        ->orderBy(DB::raw('YEAR (date), MONTH(date)'));
    });

  }

  public function getMonth(Request $request, DateRange $dr) {
    $arr = [];
    $data = $this->getAggregateByDateRange($dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d'))->all();

    foreach ($dr->monthInterval() as $key => $date) {

      $filtered = $data->filter(function ($item) use ($date){
        return $item->date->format('Y-m') == $date->format('Y-m')
          ? $item : null;
      });

      $obj = new StdClass;
      $obj->date = $date;
      $obj->dailysale = $filtered->first();
      $arr[$key] = $obj;
    }
    return collect($arr);
  }



  private function getAggregateWeekly($fr, $to) {

    $sql = 'date, MONTH(date) AS month, YEAR(date) as year, SUM(sales) AS sales, SUM(slsmtd_totgrs) AS slsmtd_totgrs, ';
    $sql .= 'WEEKOFYEAR(date) as week, YEARWEEK(date, 3) AS yearweak, ';
    $sql .= 'SUM(purchcost) AS purchcost, SUM(cos) AS cos, SUM(tips) AS tips, SUM(mancost) AS mancost, ';
    $sql .= 'SUM(custcount) AS custcount, SUM(empcount) AS empcount, SUM(headspend) AS headspend';

    return $this->scopeQuery(function($query) use ($fr, $to, $sql) {
      return $query->select(DB::raw($sql))
        ->whereBetween('date', [$fr, $to])
        ->groupBy(DB::raw('YEARWEEK(date, 3)'));
        //->orderBy(DB::raw('YEAR (date), MONTH(date)'));
    });

  }



  public function getWeek(Request $request, DateRange $dr) {
    $arr = [];
    $data = $this->getAggregateWeekly($dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d'))->all();

    //return $dr->weekInterval();

    foreach ($dr->weekInterval() as $key => $date) {

      $filtered = $data->filter(function ($item) use ($date){
        return $item->yearweak == $date->format('YW')
          ? $item : null;
      });

      $obj = new StdClass;
      $obj->date = $date;
      $obj->dailysale = $filtered->first();
      $arr[$key] = $obj;
    }
    return collect($arr);
  }



  private function getAggregateQuarterly($fr, $to) {

    $sql = 'date, QUARTER(date) as quarter, YEAR(date) as year, SUM(sales) AS sales, SUM(slsmtd_totgrs) AS slsmtd_totgrs, ';
    $sql .= 'SUM(purchcost) AS purchcost, SUM(cos) AS cos, SUM(tips) AS tips, SUM(mancost) AS mancost, ';
    $sql .= 'SUM(custcount) AS custcount, SUM(empcount) AS empcount, SUM(headspend) AS headspend';

    return $this->scopeQuery(function($query) use ($fr, $to, $sql) {
      return $query->select(DB::raw($sql))
        ->whereBetween('date', [$fr, $to])
        ->groupBy(DB::raw('YEAR (date), QUARTER(date)'));
        //->orderBy(DB::raw('YEAR (date), MONTH(date)'));
    });

  }



  public function getQuarter(Request $request, DateRange $dr) {
    $arr = [];
    $data = $this->getAggregateQuarterly($dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d'))->all();

    foreach ($dr->quarterInterval() as $key => $date) {

      $filtered = $data->filter(function ($item) use ($date){
        return ($item->quarter == $date->quarter) && ($item->year == $date->year)
          ? $item : null;
      });

      $obj = new StdClass;
      $obj->date = $date;
      $obj->dailysale = $filtered->first();
      $arr[$key] = $obj;
    }
    return collect($arr);
  }



  private function getAggregateYearly($fr, $to) {

    $sql = 'date, YEAR(date) as year, SUM(sales) AS sales, SUM(slsmtd_totgrs) AS slsmtd_totgrs, ';
    $sql .= 'SUM(purchcost) AS purchcost, SUM(cos) AS cos, SUM(tips) AS tips, SUM(mancost) AS mancost, ';
    $sql .= 'SUM(custcount) AS custcount, SUM(empcount) AS empcount, SUM(headspend) AS headspend';

    return $this->scopeQuery(function($query) use ($fr, $to, $sql) {
      return $query->select(DB::raw($sql))
        ->whereBetween('date', [$fr, $to])
        ->groupBy(DB::raw('YEAR(date)'));
        //->orderBy(DB::raw('YEAR (date), MONTH(date)'));
    });

  }


  public function getYear(Request $request, DateRange $dr) {
    $arr = [];
    $data = $this->getAggregateYearly($dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d'))->all();

    foreach ($dr->yearInterval() as $key => $date) {

      $filtered = $data->filter(function ($item) use ($date){
        return ($item->year == $date->year)
          ? $item : null;
      });

      $obj = new StdClass;
      $obj->date = $date;
      $obj->dailysale = $filtered->first();
      $arr[$key] = $obj;
    }
    return collect($arr);
  }




  public function sumByDateRange($fr, $to) {

    $sql = 'SUM(sales) AS sales, SUM(slsmtd_totgrs) AS slsmtd_totgrs, ';
    $sql .= 'SUM(purchcost) AS purchcost, SUM(cos) AS cos, SUM(tips) AS tips, ';
    $sql .= 'SUM(custcount) AS custcount, SUM(empcount) AS empcount, SUM(headspend) AS headspend, branchid';

    return $this->scopeQuery(function($query) use ($fr, $to, $sql) {
      return $query->select(DB::raw($sql))
        ->whereBetween('date', [$fr, $to]);
    });

  }
}