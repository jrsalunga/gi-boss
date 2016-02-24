<?php namespace App\Repositories;

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


  public function todayTopSales(Carbon $date, $limit=10) {


    $arr = [];
  

    $ds = DailySales::where('date', $date->format('Y-m-d'))->orderBy('sales', 'DESC')->take($limit)->get();
    
    foreach ($ds as $d) {
      $branch = Branch::where('id', $d->branchid)->get(['code', 'descriptor', 'id'])->first();
      $y = DailySales::where('date', $date->copy()->subDay()->format('Y-m-d'))->where('branchid', $d->branchid)->first();

      $s = new \StdClass;
      $c = new \StdClass;

      $s->branch = $branch;
      $s->today = $d;
      $s->yesterday = $y;
      $c->sales = ($d->sales - $y->sales);
      $s->diff = $c;

      array_push($arr, $s);
    }


    return collect($arr);
  }
}