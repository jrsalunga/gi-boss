<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Criterias\DateCriteria;
use App\Repositories\DailySalesRepository as DSRepo;
use App\Repositories\BossBranchRepository as BBRepo;
use App\Repositories\BackupRepository as BRepo;
use App\Repositories\Criterias\BossBranchCriteria;
use App\Repositories\Criterias\BranchDailySalesCriteria;
use Exception;
use Illuminate\Http\Response;
use App\Repositories\DateRange;


class DashboardController extends Controller 
{

	protected $repo;
	protected $bb;
	protected $dr;

	public function __construct(DSRepo $dsrepo, BBRepo $bbrepo, DateRange $dr, BRepo $brepo) {
		$this->repo = $dsrepo;
		$this->bb = $bbrepo;
		$this->br = $brepo;
		$this->bb->pushCriteria(new BossBranchCriteria);
		$this->dr = $dr;
	}

	private function setViewWithDR($view){
		$response = new Response($view->with('dr', $this->dr));
		$response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
		$response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
		$response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
		return $response;
	}

	public function getIndex(Request $request) {

		$backups = $this->br->with(['branch'=>function($query){
        $query->select(['code', 'descriptor', 'id']);
      }])->scopeQuery(function($query){
	   	 return $query->orderBy('uploaddate','desc')->take(10);
			})->all();

		$dailysales = $this->repo->todayTopSales($this->dr->now);
		//$dailysales = $this->repo->todayTopSales($this->dr->now->subDay(1));

		//$dailysales = $this->repo->branchByDate($this->dr->now);
		
		//return $backups;
		$view = view('index')
			->with('dailysales', $dailysales)
			->with('backups', $backups);
		return $this->setViewWithDR($view);
	}


	public function getSales(Request $request) {
		$date = carbonCheckOrNow($request->input('date'));
		return view('sales')->with('date', $date);
		//$data = $this->getDashboardCSV($request);
	}

	public function getDailySales(Request $request) {
		$this->dr->setDates($request);
		//return dd($this->dr);
		$dailysales = $this->repo->branchByDate($this->dr->date);
		return $this->setViewWithDR(view('dashboard.dailysales')->with('dailysales', $dailysales));
		//return view()->with('dr', $this->dr)->with('dailysales', $dailysales);
	}

	public function getDailySalesAll(Request $request) {
		$dailysales = $this->repo->allBranchByDate($this->dr->date);
		return $this->setViewWithDR(view('dashboard.dailysales-all')->with('dailysales', $dailysales));
		//return view('dashboard.dailysales')->with('dr', $this->dr)->with('dailysales', $dailysales);
	}



	public function getDashboardTSV(Request $request) {

		$date = carbonCheckorNow($request->input('date'));

		//echo $date->month.' - '. $date->daysInMonth .'<br>';
		$bb = $this->bb->with('branch')->all();

		echo "date\t";
		foreach ($bb as $b) {
			echo $b->branch->code."\t";
		}
		echo PHP_EOL;
		//echo '<br>';

		for ($i=1; $i <= $date->daysInMonth; $i++) { 
			
			$day = Carbon::parse($date->year.'-'.$date->month.'-'.$i);
			echo $day->format('Ymd')."\t";
			

			foreach ($bb as $b) {
				$ds = $this->repo->findWhere([
					'date'=>$day->format('Y-m-d'), 
					'branchid'=>$b->branchid
				])->first();

				echo is_null($ds) ? 0 : $ds->sales;
				echo "\t";
			}
			echo PHP_EOL;
			//echo '<br>';
			
		}

		$response = new Response;
	 	$response->header('Content-Type', 'plain/text');
  	$response->header('Content-Disposition', 'attachment; filename="data.tsv"');
	  return $response;
	}


	public function getDashboardCSV(Request $request) {

		$date = carbonCheckorNow($request->input('date'));

		//echo $date->month.' - '. $date->daysInMonth .'<br>';
		$bb = $this->bb->with('branch')->all();

		echo "Date";
		foreach ($bb as $b) {
			echo ',';
			echo $b->branch->code;
		}
		echo PHP_EOL;
		//echo '<br>';

		for ($i=1; $i <= $date->daysInMonth; $i++) { 
			
			$day = Carbon::parse($date->year.'-'.$date->month.'-'.$i);
			echo $day->format('Y-m-d').",";

			foreach ($bb as $b) {
				$ds = $this->repo->findWhere([
					'date'=>$day->format('Y-m-d'), 
					'branchid'=>$b->branchid
				])->first();

				echo is_null($ds) ? 0 : $ds->sales;
				echo $bb->last()==$b ? '':',';
				//echo ",";
			}
			echo PHP_EOL;
			//echo '<br>';
			
		}

		return;
		//$response = new Response;
	 	//$response->header('Content-Type', 'plain/text');
  	//$response->header('Content-Disposition', 'attachment; filename="data.csv"');
	  //return $response;
	}
}