<?php namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\DateRange;
use App\Repositories\EmployeeRepository as EmployeeRepo;
use App\Repositories\TimelogRepository as TimelogRepo;
use App\Repositories\Criterias\ActiveEmployeeCriteria as ActiveEmployee;
use App\Models\Employee;


class EmployeeController extends Controller 
{

	protected $employee;
	protected $timelog;

	public function __construct(EmployeeRepo $employee, TimelogRepo $timelog) {
		$this->employee = $employee;
		$this->timelog = $timelog;
	}

	private function getMancom() {

		$this->employee->pushCriteria(ActiveEmployee::class);

		return $this->employee
									->with('position')
									->orderBy('lastname')
									->orderBy('firstname')
									->findWhere(['deptid'=>'201E68D4674111E596ECDA40B3C0AA12'], ['code', 'lastname', 'firstname', 'positionid','id']);
									//->findWhereIn('deptid',
									//		['201E68D4674111E596ECDA40B3C0AA12', 'D2E8E339A47B11E592E000FF59FBB323'], 
									//		['code', 'lastname', 'firstname', 'positionid','id']
									//);
	}

	private function getTimelog($date, $empids) {
		return $this->timelog
							->with(['branch'=>function($query) {
								return $query->select('code', 'id');
							}])
							->scopeQuery(function($query) use ($date, $empids) {
								return $query->whereBetween('datetime', [
                  $date->copy()->format('Y-m-d').' 06:00:00',          // '2015-11-13 06:00:00'
                  $date->copy()->addDay()->format('Y-m-d').' 05:59:59' // '2015-11-14 05:59:59'
              	])
              	->whereIn('employeeid', $empids)
              	->where('ignore', '0')
              	->groupBy('employeeid')
              	->groupBy('branchid')
              	->groupBy(\DB::raw('HOUR(datetime)'))
              	->groupBy(\DB::raw('MINUTE(datetime)'))
              	->orderBy('datetime');
							})
							->all();
	}

	public function getWatchlist(Request $request) {

		$datas = [];

		$employees = $this->getMancom();
		$empids = $employees->pluck('id')->toArray();

		$date = $request->has('date')
			? carbonCheckorNow($request->input('date'))
			: c();

		$timelogs = $this->getTimelog($date, $empids);


		foreach ($employees as $key => $employee) {
			$datas[$key]['employee'] = $employee;
			$datas[$key]['timelogs'] = $timelogs->where('employeeid', $employee->id);

		}

		//return $datas;
		return view('dashboard.watchlist')
							->with('date', $date)
							->with('datas', $datas);
	}

	public function getWatchlistSummary(Request $request) {

		$datas = [];

		$employees = $this->getMancom();
		$empids = $employees->pluck('id')->toArray();

		$date = $request->has('date')
			? carbonCheckorNow($request->input('date'))
			: c();

		if ($date->day>15) {
			$fr = c($date->format('Y-m-').'16');
			$to = $date->copy()->endOfMonth();
		} else {
			$fr = $date->copy()->startOfMonth();
			$to = c($date->format('Y-m-').'15');
		}


		$days = [];
		$temp = $fr->copy();
		do {
			$days[$temp->day] = $temp->copy(); 
			$temp->addDay();
		} while ($temp->lte($to));

		






		foreach ($employees as $key => $employee) {
			$datas[$key]['employee'] = $employee;
			
			$total = 0;
			foreach ($days as $k => $day) {
				$i = [$employee->id];
				$timelogs = $this->getTimelog($day, $i);
			
				$datas[$key]['timelogs'][$k]['count'] = count($timelogs);
				$datas[$key]['timelogs'][$k]['date'] = $day;

				if (count($timelogs)>0)
					$total++;
				//$datas[$key]['timelogs'][$key] = $timelogs->where('employeeid', $employee->id);

			}

			$datas[$key]['total_days'] = $total;
		}

		#return $datas; 
		if ($request->has('print') && $request->input('print')=='true') {
			return view('dashboard.watchlist-summary-print')
							->with('date', $date)
							->with('days', $days)
							->with('datas', $datas);

		} else {
			return view('dashboard.watchlist-summary')
							->with('date', $date)
							->with('days', $days)
							->with('datas', $datas);
		}
	}


	public function search(Request $request, $param1=null) {

    $limit = empty($request->input('maxRows')) ? 10:$request->input('maxRows'); 
    $res = Employee::where('empstatus', '<>', '4')
    				->where('empstatus', '<>', '5')
    				->where(function ($query) use ($request) {
              $query->orWhere('code', 'like', '%'.$request->input('q').'%')
          			->orWhere('lastname', 'like',  '%'.$request->input('q').'%')
		            ->orWhere('firstname', 'like',  '%'.$request->input('q').'%')
		            ->orWhere('middlename',  'like', '%'.$request->input('q').'%')
		            ->orWhere('rfid',  'like', '%'.$request->input('q').'%');
            })
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->take($limit)
            ->get();

		return $res;
	}



}









