<?php namespace App\Http\Controllers;
use Carbon\Carbon;
use Validator;
use App\Http\Controllers\Controller;
use App\Repositories\TimelogRepository; 
use App\Repositories\EmployeeRepository; 
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Timelog;
use App\Repositories\Criterias\BossBranchCriteria;
use App\Repositories\Criterias\BossOldBranchesCriteria;
use App\Repositories\BossBranchRepository as BBRepo;
use App\Repositories\ManskeddayRepository as Manday;
use App\Repositories\ManskeddtlRepository as Mandtl;
use App\Repositories\BranchRepository;
use App\Models\Employee;
use App\Events\Timelog\Timelog as TimelogEvent;

class TimelogController extends Controller 
{

	protected $timelog;
	protected $employee;
	protected $bb;
	protected $bb_id;
	protected $mandtl;
	protected $branch;

	public function __construct(TimelogRepository $timelog, EmployeeRepository $employee, BBRepo $bbrepo, Mandtl $mandtl) {
		
		$this->employee = $employee;
		$this->bb = $bbrepo;
		$this->bb->pushCriteria(new BossBranchCriteria);
		
		$this->timelog = $timelog;
		//$this->timelog->pushCriteria(new BossOldBranchesCriteria($this->getBossBranchid()));

		$this->mandtl = $mandtl;

		$this->branch = new BranchRepository;
	}

	private function getBossBranchid() {
		$this->bb_id = $this->bb->all()->pluck('branchid')->toArray();
		return $this->bb_id;
	}

	
	public function getRoute(Request $request, $param1=null, $param2=null) {
		if (is_null($param1) && is_null($param2))
			return $this->getIndex($request);
		elseif ($param1==='employee' && is_uuid($param2))
			return $this->employeeTimelog($request, $param2);
		elseif (is_uuid($param1) && $param2==='edit')
			return $this->editTimelog($request, $param1);
		else
			return abort('404');
	}


	public function getIndex(Request $request) {

		$timelogs = $this->timelog
			->with(['employee'=>function($query){
        $query->select(['code', 'lastname', 'firstname', 'branchid', 'id', 'positionid']);
      },'branch'=>function($query){
        $query->select(['code', 'descriptor', 'id']);
      },'employee.position'=>function($query){
        $query->select(['code', 'descriptor', 'id']);
      }])
      ->orderBy('createdate', 'DESC')
      ->paginate(10);

  	return view('timelog.index', compact('timelogs'));
	}

	public function employeeTimelog(Request $request, $employeeid) {

		$employee = $this->employee
									->skipCache()
									->with(['branch'=>function($query){
        						return $query->select(['code', 'descriptor', 'id']);
        					}])
        					->with(['position'=>function($query){
        						return $query->select(['code', 'descriptor', 'id']);
        					}])
									->find($employeeid, ['code', 'lastname', 'firstname', 'branchid', 'positionid', 'id']);
									
		if (!$employee)
			return abort('404');

		$mandtl = $this->mandtl
									->skipCache()
									->whereHas('manskedday', function ($query) use ($request) {
										$query->where('date', $request->input('date'));
									})
									->with('manskedday.manskedhdr')
									->findWhere(['employeeid'=>$employeeid])
									->first();

		$date = ($request->has('date') || is_iso_date($request->input('date')))
			? carbonCheckorNow($request->input('date'))
			: c();

		$timelogs = $this->timelog->employeeTimelogs($employee, $date);

		$ts = new \App\Helpers\Timesheet;
		$timesheet = $ts->generate($employee->id, $date, $timelogs);

		return view('timelog.employee')
								->with('date', $date)
								->with('mandtl', $mandtl)
								->with('employee', $employee)
								->with('timesheet', $timesheet)
								->with('timelogs', $timelogs);
	}

	public function editTimelog(Request $request, $timelogid) {
		$timelog = $this->timelog
									->skipCriteria()
									->with(['employee'=>function($query){
        						$query->select(['code', 'lastname', 'firstname', 'positionid', 'id'])
        							->with(['position'=>function($query){
		        						$query->select(['code', 'descriptor', 'id']);
		        					}]);
        					}])
        					->with(['branch'=>function($query){
        						return $query->select(['code', 'descriptor', 'id']);
        					}])
        					->find($timelogid);

		return view('timelog.edit')
							->with('date', $timelog->getStoreDate())
							->with('timelog', $timelog);
	}

	public function put(Request $request, $id) {

		$timelog = $this->timelog->find($id);

		if ($timelog->id!=$request->input('id'))
			return redirect()->back()->withErrors(['msg'=>'Unable to save. ID not match!']);


		$attrs = [
			'txncode' => $request->input('txncode'),
			'ignore'	=> $request->has('ignore') && $request->input('ignore')=='on' ? '1':'0'
		];

		if ($request->has('txncode'))
			$timelog->txncode = $request->input('txncode');

		$timelog->ignore = $request->has('ignore') && $request->input('ignore')=='on' ? '1':'0';


		$url = '/timelog/employee/'.strtolower($timelog->employeeid).'?date='.$timelog->getStoreDate()->format('Y-m-d');
		if ($timelog->save())
			return redirect($url)->with('alert-success', 'Timelog ignored!');
		else
			return redirect($url)->with('alert-warning', 'Unable to ignore timelog!');

	}


	public function deleteEmployeeTimelog(Request $request, $employeeid) {

		if (strtoupper($employeeid) !== $request->input('employeeid'))
		 return abort('404');

		$timelog = $this->timelog->find($request->input('id'));
		
		$url = '/timesheet?date='.$request->input('date').'&branchid='.strtolower($request->input('branchid'));
		
		//Timelog::where('id', $timelog->id)->update(['ignore'=>1]);

		//return dd($this->timelog->update(['ignore'=>1], $timelog->id));

		$timelog->ignore = $request->input('ignore')=='1' ? '0':'1';
		//$timelog->save();

		//if ($this->timelog->delete($timelog->id))
		if ($timelog->save())
			return redirect($url)->with('alert-success', 'Timelog ignored!');
		else
			return redirect($url)->with('alert-warning', 'Unable to ignore timelog!');
		
	}



	public function makeAddView(Request $request) {
		$branches = $this->branch->orderBy('code')->all(['code', 'descriptor', 'id']);
		return view('timelog.add')->with('branches', $branches);	
	}


	public function manualPost(Request $request) {
		//return $request->all();
  	$rules = array(
			'employeeid'	=> 'required|max:32|min:32',
			'branchid'	=> 'required|max:32|min:32',
			'date'      	=> 'required|date',
			'time' 				=> 'required',
		);

		$messages = [
	    'employeeid.required' => 'Employee is required.',
	    'branchid.required' => 'Branch is required.',
	    'employeeid.max' => 'Employee is required..',
	    'employeeid.min' => 'Employee is required...',
	    'time.date_format' => 'The time does not match the format HH:MM. (e.g 02:30, 17:00)',
		];
		
		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails())
			return redirect('/timelog/add')->withErrors($validator);
		
		try {
			$datetime = Carbon::parse($request->input('date').' '.$request->input('time'));
		} catch (\Exception $e) {
			return redirect('/timelog/add')->withErrors(
				['message'=>'The time does not match the format 12 Hrs (01:30 PM) or 24 Hrs (13:30)']);
		}

		$employee = Employee::where('id', $request->input('employeeid'))
												//->where('branchid', $request->user()->branchid)
												->first();

		if(is_null($employee)) {
			return redirect('/timelog/add')->withErrors(
				['message' => 'Employee not found on this branch.']);
		}

		try {
			$branch = $this->branch->find($request->input('branchid'));
		} catch (\Exception $e) {
			return redirect('/timelog/add')->withErrors(
				['message'=>$e->getMessage()]);
		}


		$attributes = [
			'employeeid' 	=> $employee->id,
			'branchid' 		=> $branch->id,
			'datetime' 		=> $datetime->format('Y-m-d H:i').':'.c()->format('s'),
			'txncode' 		=> $request->input('txncode'),
			'entrytype' 	=> 2,
			'terminalid'	=> clientIP()
		];

		$timelog = $this->timelog->create($attributes);

				
		if (is_null($timelog)) {
			return redirect('/'.brcode().'/timelog/add')->withErrors(
				['message' => 'Unable to save timelog.']);
		} else {

			if (app()->environment()==='production')
				event(new TimelogEvent($timelog, $employee));
			
			$uri = is_null(request()->input('ref')) ? '/timelog/add':'/'.brcode().'/timesheet';
			return redirect($uri)->with('alert-success', $employee->lastname.', '. $employee->lastname.' timelog saved!');
		}

		return $timelog;
  }




}