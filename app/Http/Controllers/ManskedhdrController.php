<?php namespace App\Http\Controllers;

use StdClass;
use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\Criterias\BossBranchCriteria;
use App\Repositories\Criterias\BossOldBranchesCriteria;
use App\Repositories\ManskedhdrRepository as ManskedRepo;
use App\Repositories\ManskeddayRepository as MandayRepo;
use App\Repositories\BossBranchRepository as BBRepo;
use App\Repositories\EmployeeRepository as EmployeeRepo;


class ManskedhdrController extends Controller 
{ 
	protected $mansked;
	protected $bb_id;
	protected $bb;
	protected $employee;
	protected $manday;

	public function __construct(ManskedRepo $mansked, BBRepo $bb, EmployeeRepo $employee, MandayRepo $manday) {
		$this->employee = $employee;
		$this->bb = $bb;
		$this->bb->pushCriteria(new BossBranchCriteria);
		$this->mansked = $mansked;
		//$this->mansked->pushCriteria(new BossOldBranchesCriteria($this->getBossBranchid()));;
		$this->manday = $manday;
	}

	private function getBossBranchid() {
		$this->bb_id = $this->bb->all()->pluck('branchid')->toArray();
		return $this->bb_id;
	}

	public function getRoute(Request $request, $param1=null) {

		if (!is_null($param1) && is_uuid($param1))
			return $this->getView($request, $param1);
		else	
			return $this->getIndex($request);
	}

	private function getIndex(Request $request) {

		$manskeds = $this->mansked
										->skipCache()
										->orderBy('year', 'desc')
										->orderBy('weekno', 'desc')
										->paginate(10);
		
		return view('mansked.index')->with('manskeds', $manskeds);
	}


	private function getView(Request $request, $manskedid) {
		$mansked = $this->mansked
									->skipCache()
									->with(['branch'=>function($query){
										return $query->select(['code', 'descriptor', 'id']);
									}])
									->with('manskeddays')
									->find($manskedid);

		//return $mansked;
		$mandtls = collect(array_collapse($mansked->manskeddays->pluck('manskeddtls')->toArray()));
		$empsOnMansked = $mandtls->pluck('employeeid')
														->unique()
														->values()
														->all();
		
		$depts = $this->employee->byDeptFrmEmpIds($empsOnMansked);

		$days = $mansked->manskeddays;
  	$manskeddays = [];
		for($h=0; $h<count($depts); $h++) {
			$arr = $depts[$h]['employees']->toArray(); // extract emp on each dept
			for($i=0; $i<count($arr); $i++) {
				for($j=0; $j<count($days); $j++) {
					
					$manskeddays[$j]['date'] = $days[$j]->date;
					$manskeddays[$j]['id'] = strtolower($days[$j]->id);

					$mandtl = $mandtls
										->where('employeeid', $depts[$h]['employees'][$i]->id)
  									->where('mandayid', $days[$j]->id)
  									->first();

					$manskeddays[$j]['mandtl'] = count($mandtl) > 0 ? $mandtl:
							['timestart'=>0, 'timeend'=>0, 'loading'=>0];
				}
				$depts[$h]['employees'][$i]['manskeddays'] = $manskeddays;
			}
		}

		if ($request->has('print'))
			return view('mansked.view-print')->with('depts', $depts)->with('mansked', $mansked);
		else
			return view('mansked.view')->with('depts', $depts)->with('mansked', $mansked);
	}

	public function getManday(Request $request, $mandayid) {

		$manday = $this->manday
								->skipCache()
								->with('manskeddtls')
								->with(['manskedhdr.branch'=>function($query){
										return $query->select(['code', 'descriptor', 'id']);
									}])
								->find($mandayid);

		$depts = $this->byDeptFrmEmpIds($manday);

		//return $this->hourlyDuty($depts);

		return view('mansked.manday.view')
							->with('depts', $depts)
							->with('manday', $manday)
							->with('hours', $this->hourlyDuty($depts));
	}



	private function byDeptFrmEmpIds($manday){ 

		$empsOnMansked = $manday->manskeddtls->pluck('employeeid');
		
		$depts = $this->employee->byDeptFrmEmpIds($empsOnMansked->toArray());

		$mandtls = $manday->manskeddtls;

		for($h=0; $h<count($depts); $h++){
				$arr = $depts[$h]['employees']->toArray(); // extract emp on each dept
				for($i=0; $i<count($arr); $i++){
					
					$mandtl = $mandtls
										->where('employeeid', $depts[$h]['employees'][$i]->id)
  									->where('mandayid', $manday->id)
  									->first();
					
					$depts[$h]['employees'][$i]['manskeddtl'] = count($mandtl) > 0 ?
						['daytype'=> $mandtl->daytype, 
						'timestart'=>$mandtl->timestart,
						'breakstart'=>$mandtl->breakstart,
						'breakend'=>$mandtl->breakend,
						'timeend'=>$mandtl->timeend,
						'workhrs'=>$mandtl->workhrs,
						'breakhrs'=>$mandtl->breakhrs,
						'loading'=>$mandtl->loading, 
						'id'=>$mandtl->id]: 
						['daytype'=> 0, 
						'timestart'=>'off',
						'breakstart'=>'',
						'breakend'=>'',
						'timeend'=>'',
						'workhrs'=>'',
						'breakhrs'=>'',
						'loading'=>'', 
						'id'=>''];
				}
			}
		return $depts;
	}

	// for $this->makeSingleView
	private function hourlyDuty($depts){
		//return config('giligans.hours');
		//return $hrs = $this->getHour('19:00', '1:00');

		

		$arr = [];
		$sorted = [];

		foreach($depts as $dept){
			for($i = 0; $i < count($dept['employees']); $i++){
      	if($dept['employees'][$i]['manskeddtl']['daytype'] == 1){

      		$ts = $dept['employees'][$i]['manskeddtl']['timestart'];
      		$bs = $dept['employees'][$i]['manskeddtl']['breakstart'];
      		$be = $dept['employees'][$i]['manskeddtl']['breakend'];
      		$te = $dept['employees'][$i]['manskeddtl']['timeend'];

      		

      		if($ts!='off' && $bs!='off' && $be!='off' && $te!='off'){
      			$hrs = $this->getHour($ts, $te);
	      		foreach ($hrs as $hr) {
	      			if(array_key_exists('hr_'.$hr, $arr)) {
	      				$arr['hr_'.$hr] += 0;
							} else {
								$arr['hr_'.$hr] = 0;
							}
	      		}
      		}

      		if($ts!='off' && $bs!='off'){
      			$this->consoHours($ts, $bs, $arr);
      		}
      		
      		if($be!='off' && $te!='off'){
      			$this->consoHours($be, $te, $arr);
	      	}

	      	if($ts!='off' && $te!='off' && $bs=='off' && $be=='off'){
	      		$this->consoHours($ts, $te, $arr);
	      	}
      	
      	}
      }
    } 
   	
    foreach($arr as $key => $value){ 
      $x = explode('_', $key);
      $sorted[$x[1]] = $value;
    }
    ksort($sorted);

    $arr = [];

    foreach (config('giligans.hours') as $key => $value) {
    	if (array_key_exists($value, $sorted)) {
    		$arr['_'.$value] = $sorted[$value];
    	}
    }
    /*
    foreach ($sorted as $key => $value) {
    	$idx = array_search($key, config('giligans.hours'));
    	$arr[$idx] = $value;
    }
		*/

    return $arr;
	}

	// for $this->hourlyDuty
	private function consoHours($s, $e, &$arr){
		$hrs = $this->getHour($s, $e);
		foreach ($hrs as $hr) {
			if(array_key_exists('hr_'.$hr, $arr)) {
				$arr['hr_'.$hr] += 1;
			} else {
				$arr['hr_'.$hr] = 1;
			}
		}
	}

	// for $this->consoHours
	private function getHour($start, $end){
		$arr = [];
		$hrs = config('giligans.hours');

		if($start!='off' || $start!='0.00' || !empty($start) || $end!='0.00' || !empty($end)){
			$s = explode(':', $start);  // 23
			$e = explode(':', $end);    // 1

			$f = array_search($s[0], $hrs);
			$t = array_search($e[0], $hrs);

			for ($f; $f<$t; $f++)
				$arr[] = $hrs[$f];

		}
		
		return $arr;
	}





}