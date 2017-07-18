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



class EmployeeController extends Controller 
{

	protected $employee;
	protected $timelog;

	public function __construct(EmployeeRepo $employee, TimelogRepo $timelog) {
		$this->employee = $employee;
		$this->timelog = $timelog;
	}

	public function getWatchlist(Request $request) {

		$datas = [];

		$this->employee->pushCriteria(ActiveEmployee::class);

		$employees = $this->employee
									->with('position')
									->orderBy('lastname')
									->orderBy('firstname')
									->findWhere(['deptid'=>'201E68D4674111E596ECDA40B3C0AA12'], ['code', 'lastname', 'firstname', 'positionid','id']);
									//->findWhereIn('deptid',
									//		['201E68D4674111E596ECDA40B3C0AA12', 'D2E8E339A47B11E592E000FF59FBB323'], 
									//		['code', 'lastname', 'firstname', 'positionid','id']
									//);

		$empids = $employees->pluck('id')->toArray();

		$date = $request->has('date')
			? c($request->input('date'))
			: c();

		$timelogs = $this->timelog
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


		foreach ($employees as $key => $employee) {
			$datas[$key]['employee'] = $employee;
			$datas[$key]['timelogs'] = $timelogs->where('employeeid', $employee->id);

		}

		//return $datas;
		return view('dashboard.watchlist')
							->with('date', $date)
							->with('datas', $datas);
	}



}









