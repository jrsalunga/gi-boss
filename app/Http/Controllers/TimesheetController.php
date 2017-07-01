<?php namespace App\Http\Controllers;

use StdClass;
use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\Criterias\BossBranchCriteria;
use App\Repositories\BossBranchRepository as BBRepo;
use App\Repositories\TimelogRepository as Timelog;
use App\Repositories\BranchRepository;
use App\Repositories\EmployeeRepository as EmployeeRepo;
use App\Repositories\ManskeddtlRepository as MandtlRepo;

class TimesheetController extends Controller 
{ 
	public $timelog;
	protected $dr;
	protected $bb;
	protected $ab;
	protected $employee;
	protected $mandtl;

	public function __construct(DateRange $dr, Timelog $timelog, BBRepo $bbrepo, EmployeeRepo $employee, MandtlRepo $mandtl) {
		$this->mandtl = $mandtl;
		$this->employee = $employee;
		$this->timelog = $timelog;
		$this->dr = $dr;
		$this->bb = $bbrepo;
    //$this->bb->pushCriteria(new BossBranchCriteria);
    $this->branch = new BranchRepository;
    $this->ab = $this->getAMbranches();
	}

	public function getRoute(Request $request, $param1=null) {
		if(!is_null($param1) && $param1=='print')
			return $this->getPrintIndex($request);
		else if(!is_null($param1) && is_uuid($param1))
			return $this->getEmployeeDtr($request, $param1);
		else
			return $this->getIndex($request);
	}

	private function getAMbranches() {
    return $this->branch
      ->orderBy('code')
      ->findWhereIn('id', 
        $this->bb->all()->pluck('branchid')->toArray(),
      ['code', 'descriptor', 'id']);
  }



	private function getIndex(Request $request){
	
		$data = null;
		$branch = null;

		$date = is_null($request->input('date')) 
			? $this->dr->now 
			: carbonCheckorNow($request->input('date'));
		
		$this->dr->date = $date;
		
		if ($request->has('branchid')) {
			
			try {
	      $branch = $this->branch->find(strtolower($request->input('branchid')));
	    } catch (Exception $e) {
	    	$branch = null;
	    }
			
			if ($branch && in_array($branch->id, $this->ab->pluck('id')->toArray()))
				$data = $this->timelog->allByDate($date, $branch->id);
			else
				$branch = null;
		
		}
		
		return $this->setViewWithDR(view('timesheet.index')
																	->with('dr', $this->dr)
																	->with('data', $data)
																	->with('branch', $branch)
																	->with('branches', $this->ab));
	}

	
	public function employeeTimesheet(Request $request, $employeeid) {

		$employee = $this->employee
									//->skipCache()
									->with(['branch'=>function($query){
        						return $query->select(['code', 'descriptor', 'id']);
        					}])
        					->with(['position'=>function($query){
        						return $query->select(['code', 'descriptor', 'id']);
        					}])
									->find($employeeid, ['code', 'firstname', 'lastname', 'positionid', 'branchid', 'id']);

		

		if (!$employee)
			return abort('404');

		$tot_tardy = 0;

		foreach ($this->dr->dateInterval() as $key => $date) {
			$timesheets[$key]['date'] = $date;
			//$timesheets[$key]['timelog'] = [];
			
			$timelogs = $this->timelog
			->skipCriteria()
			->getRawEmployeeTimelog($employeeid, $date, $date)
			->all();

			$mandtl = $this->mandtl
									->skipCache()
									->whereHas('manskedday', function ($query) use ($date) {
										return $query->where('date', $date->format('Y-m-d'));
									})
									->findWhere(['employeeid'=>$employee->id])
									->first();
	
			$timesheets[$key]['mandtl'] = $mandtl;
			$timesheets[$key]['timelog'] = $this->timelog->generateTimesheet($employee->id, $date, collect($timelogs));



      $tardy = 0;
      if ((isset($timesheets[$key]['mandtl']->timestart) && $timesheets[$key]['mandtl']->timestart!='off') 
      && !is_null($timesheets[$key]['timelog']->timein)) {

        $timein = $timesheets[$key]['timelog']->timein->timelog->datetime;
        $timestart = c($timein->format('Y-m-d').' '.$timesheets[$key]['mandtl']->timestart);
        
        $late =$timestart->diffInMinutes($timein, false); 
        $tardy = $late>0 ? number_format(($late/60), 2) : 0;
        //$tardy = 1;

        if($tardy>0) {
          $tot_tardy+=$tardy;
        }

			}
      $timesheets[$key]['tardy'] = $tardy;
		}

		//return $timesheets;

		$header = new StdClass;
		$header->totalWorkedHours = collect($timesheets)->pluck('timelog')->sum('workedHours');
		$header->totalTardyHours = number_format($tot_tardy, 2);

		return 	$this->setViewWithDR(
							view('timesheet.employee')
							->with('timesheets', $timesheets)
							->with('employee', $employee)
							->with('header', $header)
							->with('dr', $this->dr)
						);




	}




	private function getEmployeeDtr(Request $request, $employeeid) {

		$employee = Employee::findOrFail($employeeid);

		foreach ($this->dr->dateInterval2() as $key => $date) {
			
			$timesheets[$key]['date'] = $date;
			
			$timelogs = $this->timelog
			->skipCriteria()
			->getRawEmployeeTimelog($employeeid, $date, $date)
			->all();
	
			//array_push($timesheets[$key]['timelog'], $this->timelog->generateTimesheet($employee->id, $date, collect($timelogs)));
			$timesheets[$key]['timelog'] = $this->timelog->generateTimesheet($employee->id, $date, collect($timelogs));
		}

		$header = new StdClass;
		$header->totalWorkedHours = collect($timesheets)->pluck('timelog')->sum('workedHours');

		return 	$this->setViewWithDR(
							view('timesheet.employee-dtr')
							->with('timesheets', $timesheets)
							->with('employee', $employee)
							->with('header', $header)
							->with('dr', $this->dr)
						);
	}


	private function setViewWithDR($view){
		$response = new Response($view->with('dr', $this->dr));
		$response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
		$response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
		$response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
		return $response;
	}



}

