<?php namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Criterias\DateCriteria;
use App\Repositories\Criterias\BossBranchCriteria;
use App\Repositories\Criterias\BranchDailySalesCriteria;
use App\Repositories\Criterias\ActiveBranchCriteria as ActiveBranch;
use App\Repositories\BackupRepository as BRepo;
use App\Repositories\DailySalesRepository as DSRepo;
use App\Repositories\BossBranchRepository as BBRepo;
use App\Repositories\DateRange;
use App\Models\Branch;
use App\Models\Backup;
use App\Repositories\BranchRepository;
use Illuminate\Container\Container as App;
use Illuminate\Support\Collection;



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
		$this->branch = new BranchRepository(new App, new Collection);
		$this->branch->pushCriteria(new ActiveBranch);
	}

	private function setViewWithDR($view){
		$response = new Response($view->with('dr', $this->dr));
		$response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
		$response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
		$response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
		return $response;
	}

	private function delinquent(Request $request){
		//$branchs = Branch::orderBy('code')->get(['code', 'descriptor', 'id']);
		$branchs = $this->branch->all(['code', 'descriptor', 'id']);
	
		$arr = [];
		$arr_wl = [];
		$arr_wo = [];
		$arr_wd = [];
		
		foreach ($branchs as $key => $branch) {
			$backup = Backup::where('branchid', $branch->id)
									->where('processed', 1)
									->where('filename', 'like', 'GC%')
									->orderBy('year', 'DESC')
									->orderBy('month', 'DESC')
									->orderBy('filename', 'DESC')
									->first(['filename', 'uploaddate']);

			if(is_null($backup)) {
				array_push($arr_wo, [
					'code'				=> $branch->code,
					'descriptor' 	=> $branch->descriptor,
					'branchid' 		=> $branch->id,
					'filename' 		=> null,
					'uploaddate' 	=> null,
					'date' 				=> null,
				]);
			} else {

				//$d = filename_to_date2($backup->filename);
				//$date = Carbon::parse($d->format('Y-m-d').' '.$backup->uploaddate->format('H:i:s'));
				
				$a = [
					'code'				=> $branch->code,
					'descriptor' 	=> $branch->descriptor,
					'branchid' 		=> $branch->id,
					'filename' 		=> $backup->filename,
					'uploaddate' 	=> $backup->uploaddate,
					'date' 				=> $backup->date,
				];

				$diff = $backup->date->diffInDays($this->dr->now, false); 

				if($diff > 1)
					array_push($arr_wd, $a); // push delinquent
				else
					array_push($arr_wl, $a); // push latest
			}
		}

		$arr_wo = array_values(array_sort($arr_wo, function ($value) {
    	return $value['code'];
		}));

		$arr_wl = array_values(array_reverse(array_sort($arr_wl, function ($value) {
    	return $value['uploaddate'];
		})));

		$arr_wd = array_values(array_sort($arr_wd, function ($value) {
    	return $value['date'];
		}));

		return collect([$arr_wo, $arr_wl, $arr_wd]);
	}

	public function getIndex(Request $request) {
		/*
		$backups = $this->br->with(['branch'=>function($query){
        $query->select(['code', 'descriptor', 'id']);
      }])->scopeQuery(function($query){
	   	 return $query->orderBy('uploaddate','desc')->take(10);
			})->all();
		*/
		$dailysales = $this->repo->todayTopSales($this->dr->now);
		
		$delinquents = $this->delinquent($request);
		//return $delinquents;
		$view = view('index')
			->with('dailysales', $dailysales)
			//->with('backups', $backups)
			->with('delinquents', $delinquents);
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

	public function getDailyRangeSalesAll(Request $request) {
		$dailysales = $this->repo->skipCache()->allBranchByDateRange($this->dr->fr, $this->dr->to);
		return $this->setViewWithDR(view('dashboard.dailysales-dr-all-deliverpct')->with('dailysales', $dailysales));
    return $this->setViewWithDR(view('dashboard.dailysales-dr-all')->with('dailysales', $dailysales));
		//return view('dashboard.dailysales')->with('dr', $this->dr)->with('dailysales', $dailysales);
	}

	public function getDailySalesAll(Request $request) {
		$dailysales = $this->repo->allBranchByDate($this->dr->date);
		return $this->setViewWithDR(view('dashboard.dailysales-all')->with('dailysales', $dailysales));
		//return view('dashboard.dailysales')->with('dr', $this->dr)->with('dailysales', $dailysales);
	}

  public function getDeliverySalesAll(Request $request) {
    $this->dr->setDates($request);
    $dailysales = $this->repo->allBranchByDate($this->dr->date);
    return $this->setViewWithDR(view('dashboard.deliverysales-all')->with('dailysales', $dailysales));
  }

  public function getDeliveryRangeSalesAll(Request $request) {
    $dailysales = $this->repo->skipCache()->allBranchByDateRange($this->dr->fr, $this->dr->to);
    return $this->setViewWithDR(view('dashboard.deliverysales-dr-all')->with('dailysales', $dailysales));
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

  public function getSalesAll(Request $request) {
    return $request;
  }

  public function getThrendsDaily(Request $request) {

    // return dd((($request->has('fr') && $request->has('to')) && (is_iso_date($request->input('fr')) && is_iso_date($request->input('to')))));


    if (($request->has('fr') && $request->has('to')) && (is_iso_date($request->input('fr')) && is_iso_date($request->input('to')))) {

      $this->dr->fr = carbonCheckorNow($request->input('fr'));
      $this->dr->to = carbonCheckorNow($request->input('to'));

      if ($this->dr->fr->gt($this->dr->to))
        return 'fr is gt to';

      $len = $this->dr->to->diffInDays($this->dr->fr);
    } else {

      $date = carbonCheckorNow($request->input('date'));
      $len = 6;
      if ($request->has('len') && $request->input('len')>0 && $request->input('len')<90)
        $len = $request->input('len');

      $this->dr->to = $date;
      $this->dr->fr = $date->copy()->subDays($len);
    } 

    $datas = [];


    // return $this->dr->to->diffInDays($this->dr->fr);

    // return $this->dr->fr;
    // return $this->dr->fr->copy()->subDay();

    $dailysales = $this->repo
                    // ->skipCache()
                    ->getAllByDr($this->dr->fr->copy()->subDay(), $this->dr->to, ['*']);

    // $branchs =  \App\Models\Boss\Branch::select(['code', 'descriptor', 'id'])->active()->orderBy('code')->get();
    $branchs =  \App\Models\Branch::select(['code', 'descriptor', 'id'])->whereIn('id', collect($dailysales->pluck('branchid'))->unique()->toArray())->orderBy('code')->get();

    foreach($branchs as $key => $branch) {
      $datas[$key]['code'] = $branch->code;
      $datas[$key]['descriptor'] = $branch->descriptor;

      $to_date = $this->dr->to->copy();
      for ($i=0; $i<=$len+1; $i++) {
        $to = $to_date->copy()->subDay($i);
        
        $datas[$key]['dss'][$i]['date'] = $to;

        $filtered = $dailysales->filter(function ($item) use ($to, $branch) {
          return ($item->branchid == $branch->id) && ($item->date->format('Y-m-d') == $to->format('Y-m-d'))
          ? $item : null;
        });

        $f = $filtered->first();

        $datas[$key]['dss'][$i]['sales'] = is_null($f) ? NULL : $f->sales;
      }
    }


      // return $datas;


    foreach($datas as $j => $data) {
      foreach($data['dss'] as $k => $ds) {
        if ($k==0 && is_null($datas[$j]['dss'][$k]['sales'])) {
          $prev_sales = NULL;
        } else {
          if (($k)<=$len)
            $prev_sales = is_null($datas[$j]['dss'][($k+1)]['sales']) ? 0 : $datas[$j]['dss'][($k+1)]['sales'];
          else
            $prev_sales = 0;
        }
        $datas[$j]['dss'][$k]['prev_sales'] = $prev_sales;
        $datas[$j]['dss'][$k]['diff'] = $datas[$j]['dss'][$k]['sales'] - $prev_sales;
        $datas[$j]['dss'][$k]['pct'] = $prev_sales>0 ? ($datas[$j]['dss'][$k]['diff']/$prev_sales)*100 : 0;
      }
    }

    foreach($datas as $l => $data) {
      unset($datas[$l]['dss'][$len+1]);
    }

    // return $datas;

    if (!in_array($request->user()->id, ['41F0FB56DFA811E69815D19988DDBE1E', '11E943EA14DDA9E4EAAFBD26C5429A67'])) {

      $email = [
        'body' => $request->user()->name.' '.$this->dr->fr->format('Y-m-d').' '.$this->dr->to->format('Y-m-d')
      ];

      \Mail::queue('emails.notifier', $email, function ($m) {
        $m->from('giligans.app@gmail.com', 'GI App - Boss');
        $m->to('freakyash_02@yahoo.com')->subject('Sales Trend');
      });
    }


    $view = view('report.trends-all-daily')
            ->with('datas', $datas);
    return $this->setViewWithDR($view);

    return $datas[0]['dss'];
  }
}