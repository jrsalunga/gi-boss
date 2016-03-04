<?php namespace App\Repositories;

use StdClass;
use Carbon\Carbon;
use App\Models\DailySales;
use App\Models\Branch;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\BossBranchRepository as BBRepo;
use App\Repositories\Criterias\BossBranchCriteria;
use App\Repositories\Criterias\BranchDailySalesCriteria;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Collection;

class DailySalesRepository extends BaseRepository {

	
	public $bossbranch;

	public function __construct(Application $app, BBRepo $bbrepo, DailySales $dailysales) {
		parent::__construct($app);
		$this->bossbranch = $bbrepo;
		$this->bossbranch->pushCriteria(new BossBranchCriteria);
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



  public function allBranchByDate(Carbon $date) {
    $ads = []; // array of dailysales
    $bb = Branch::orderBy('code', 'ASC')->get();

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


  public function todayTopSales(Carbon $date, $limit=10) {

    $arr = [];
    $ds_null = false;

    $ds = DailySales::where('date', $date->format('Y-m-d'))->orderBy('sales', 'DESC')->take($limit)->get();
    
    if(count($ds)=='0') {
      $ds = DailySales::where('date', $date->copy()->subDay()->format('Y-m-d'))->orderBy('sales', 'DESC')->take($limit)->get();
      $ds_null = true;
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
        $c->sales1 = ($ds_yesteday->sales - $ds_otherday->sales);
      $s->diff = $c;

      array_push($arr, $s);
    }


    return collect($arr);
  }
}