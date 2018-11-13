<?php namespace App\Http\Controllers\Hr;

use DB;
use Mail;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\EmployeeRepository as EmployeeRepo;
use App\Repositories\BranchRepository as BranchRepo;
use App\Repositories\Hris\CompanyRepository as CompanyRepo;
use App\Repositories\PositionRepository as PositionRepo;
use App\Repositories\DepartmentRepository as DepartmentRepo;
use App\Repositories\StatutoryRepository as StatutoryRepo;
use App\Repositories\ReligionRepository as ReligionRepo;
use App\Helpers\EmpCreator;
use App\Models\Employee;
use MoneyToWords\MoneyToWordsConverter as Convert;
use App\Http\Controllers\EmpController as EmpCtrl;


class EmployeeController extends Controller
{

	protected $dr;
	protected $employee;
	protected $branch;
	protected $company;
	protected $position;
	protected $department;
	protected $statutory;
	protected $religion;

	public function __construct(EmployeeRepo $employeeRepo, BranchRepo $branchRepo, CompanyRepo $companyRepo, PositionRepo $positionRepo, DepartmentRepo $departmentRepo, StatutoryRepo $statutoryRepo, ReligionRepo $religionRepo) {
		$this->employee = $employeeRepo;
		$this->branch = $branchRepo;
		$this->company = $companyRepo;
		$this->position = $positionRepo;
		$this->department = $departmentRepo;
		$this->statutory = $statutoryRepo;
		$this->religion = $religionRepo;
	}


	public function create(Request $request) {
		return view('hr.masterfiles.employee.create')->with('code', $this->employee->getLatestCode());
	}


	public function show(Request $request, $id) {
		$employee = $this->employee->codeID($id);

		if ($request->has('raw') && $request->input('raw')=='data')
			return $employee;
		return is_null($employee) ? abort('404') : view('hr.masterfiles.employee.view')->with('employee', $employee);
	}

	public function store(Request $request) {

		$this->middleware('sanitize');

		if ($request->has('_raw'))
			return $request->all();
			
		if ($request->has('_type')) {
			switch ($request->input('_type')) {
				case 'quick':
					return $this->process_quick($request);
					break;
				case 'full':
					return $this->process_full($request);
					break;
				case 'employment':
					return $this->process_employment($request);
					break;
				case 'personal':
					return $this->process_personal($request);
					break;
				case 'family':
					return $this->process_family($request);
					break;
				case 'workedu':
					return $this->process_workedu($request);
					break;
				case 'confirm':
					return $this->process_confirm($request);
					break;
				case 'update_general':
					return $this->update_general($request);
					break;
			}
		} 
		return app()->environment('local') ? 'Honeypot not found!' : abort('404'); 
	}


	private function process_quick(Request $request) {
		
		$rules = [
      'code' 				=> 'regex:/^\d{1,6}$/',
      'lastname' 		=> 'required|max:30|anshup',
      'firstname' 	=> 'required|max:30|anshup',
      'middlename' 	=> 'max:30|anshup',
    ];

		$this->validate($request, $rules);

		// set man_no if no code/man_no given by the user
		if (!$request->has('code'))
			$request->request->add(['code'=>$this->employee->getLatestCode()]);
		else
			$request->merge(['code'=>pad($request->input('code'),6)]);

		$o = $this->employee->findWhere(['code'=>$request->input('code')])->first();
		if (!is_null($o))
			return redirect()->back()
										->withInput($request->input())
										->withErrors('Man No. '.$request->input('code').' is already in used.');


		$keys = array_keys($rules);

		DB::beginTransaction();

		try {
    	$new = $this->employee->create($request->only($keys));
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		DB::commit();
		if ($request->input('_submit')==='next')
    	return redirect('/hr/masterfiles/employee/'.$new->lid().'/edit/employment');
  	else
    	return redirect('/hr/masterfiles/employee/'.$new->lid())->with('alert-success','Record saved!');
	}

	private function update_general(Request $request) {

		$rules = [
      'lastname' 		=> 'required|max:30|anshup',
      'firstname' 	=> 'required|max:30|anshup',
      'middlename' 	=> 'max:30|anshup',
    ];

		$this->validate($request, $rules);

		//return $request->all();
		$o = $this->employee->find($request->input('id'));
		if (is_null($o))
			return redirect()->back()->withErrors('Employee not found.');

		$keys = array_keys($rules);
		unset($rules['id']);

		DB::beginTransaction();

		try {
    	$new = $this->employee->update($request->only($keys), $o->id);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		DB::commit();
    if ($request->input('_submit')==='next')
    	return redirect('/hr/masterfiles/employee/'.$new->lid().'/edit/employment');
  	else
    	return redirect('/hr/masterfiles/employee/'.$new->lid())->with('alert-success','Record saved!');
	}

	private function process_employment(Request $request) {

		$rules = [
			'companyid' 	=> 'required|max:32|alpha_num',
      'branchid' 		=> 'required|max:32|alpha_num',
      'positionid' 	=> 'required|max:32|alpha_num',
      'deptid'			=> 'required|max:32|alpha_num',
      'empstatus' 	=> 'required|regex:/^[0-5]{1}$/',
      'datestart' 	=> 'required|date_format:Y-m-d',
      'paytype' 		=> 'required|regex:/^[0-3]{1}$/',
      'ratetype' 		=> 'required|regex:/^[0-2]{1}$/',
      'punching' 		=> 'max:100',
      'rate' 				=> 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/',
      'ecola' 			=> 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/',
      'allowance1' 	=> 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/',
      'allowance2' 	=> 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/',
      //'sssno'			 	=> 'required|regex:/^\d{10}$/',
      //'phicno' 			=> 'regex:/^\d{12}$/',
      //'hdmfno' 			=> 'regex:/^\d{12}$/',
      //'tin'					=> 'regex:/^\d{12}$/',
      'id' 					=> 'required|max:32|alpha_num',
    ];

    if ($request->input('empstatus')=='2') {
    	$rules['datehired'] = 'required|date_format:Y-m-d';
    } else
    	$rules['datehired'] = 'date_format:Y-m-d';

     if ($request->input('empstatus')=='3')
    	$rules['date_reg'] = 'required|date_format:Y-m-d';
    else
    	$rules['date_reg'] = 'date_format:Y-m-d';

    if (in_array($request->input('empstatus'), ['4', '5', '6']))
    	$rules['datestop'] = 'required|date_format:Y-m-d';
    else
    	$rules['datestop'] = 'date_format:Y-m-d';


    $rules2 = [
    	'date_reg'		=> 'date_format:Y-m-d',
      'meal' 			  => 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/',
      'ee_sss'		  => 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/',
      'er_sss'			=> 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/',
      'sss_tag'			=> 'regex:/^\d{1}$/',
      'ee_phic' 		=> 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/',
      'er_phic' 		=> 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/',
      'phic_tag'		=> 'regex:/^\d{1}$/',
      'ee_hdmf' 		=> 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/',
      'er_hdmf' 		=> 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/',
      'hdmf_tag'		=> 'regex:/^\d{1}$/',
      'ee_tin' 			=> 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/',
      'er_tin' 			=> 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/',
      'wtax' 				=> 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/',
      'wtax_tag'		=> 'regex:/^\d{1}$/',
      'employee_id' => 'max:32|alpha_num',
    ];


		if ($request->input('sss_tag')) {
			$rules['sssno'] 	= 'required|regex:/^\d{10}$/';
      $rules['ee_sss'] = 'required|regex:/^(?!$)(?:[1-9]\d{0,5})?(?:\.\d{1,2})?$/';
      $rules['er_sss'] = 'required|regex:/^(?!$)(?:[1-9]\d{0,5})?(?:\.\d{1,2})?$/';
		} else {
			$rules['sssno'] 	= 'regex:/^\d{10}$/';
      $rules['ee_sss'] = 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/';
      $rules['er_sss']	= 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/';
		}

		if ($request->input('phic_tag')) {
			$rules['phicno'] 	= 'required|regex:/^\d{12}$/';
      $rules['ee_phic'] = 'required|regex:/^(?!$)(?:[1-9]\d{0,5})?(?:\.\d{1,2})?$/';
      $rules['er_phic'] = 'required|same:ee_phic|regex:/^(?!$)(?:[1-9]\d{0,5})?(?:\.\d{1,2})?$/';
		} else {
			$rules['phicno'] 	= 'regex:/^\d{12}$/';
      $rules['ee_phic'] = 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/';
      $rules['er_phic']	= 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/';
		}

		if ($request->input('hdmf_tag')) {
			$rules['hdmfno'] 	= 'required|regex:/^\d{12}$/';
      $rules['ee_hdmf'] = 'required|regex:/^(?!$)(?:[1-9]\d{0,5})?(?:\.\d{1,2})?$/';
      $rules['er_hdmf'] = 'required|same:ee_hdmf|regex:/^(?!$)(?:[1-9]\d{0,5})?(?:\.\d{1,2})?$/';
		} else {
			$rules['hdmfno'] 	= 'regex:/^\d{12}$/';
      $rules['ee_hdmf'] = 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/';
      $rules['er_hdmf']	= 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/';
		}

		if ($request->input('wtax_tag')) {
			$rules['tin'] 	= 'required|regex:/^\d{12}$/';
      $rules['wtax'] = 'required|regex:/^(?!$)(?:[1-9]\d{0,5})?(?:\.\d{1,2})?$/';
		} else {
			$rules['tin'] 	= 'regex:/^\d{12}$/';
      $rules['wtax'] = 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/';
		}


    $this->clean_request_number_format($request, ['meal','rate', 'ecola', 'allowance1', 'allowance2', 'ee_sss', 'er_sss', 'ee_phic', 'er_phic', 'ee_hdmf', 'er_hdmf', 'ee_tin', 'er_tin', 'wtax']);
    $this->clean_request_govmt($request, ['sssno', 'hdmfno', 'tin']);

		$this->validate($request, $rules);
		//$this->validate($request, $rules2);

		$o = $this->employee->find($request->input('id'));
		if (is_null($o))
			return redirect()->back()->withErrors('Employee not found.');

		unset($rules['id']);
		foreach ($rules2 as $key => $value) {
			if (array_key_exists($key, $rules))
				unset($rules[$key]);
		}
		$keys = array_keys($rules);

		if (array_key_exists($request->input('positionid'), config('giligans.position')))
			$request->merge(['punching'=>config('giligans.position')[$request->input('positionid')]['ordinal']]);
		else
			$request->merge(['punching'=>99]);

		//return $request->all();
		//return $request->only($keys);
		DB::beginTransaction();

		try {
    	$new = $this->employee->update($request->only($keys), $o->id);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}


		$request->merge(['employee_id'=>$o->id]);
		//return $o->id;
		//return $request->only(array_keys($rules2));
		try {
			$s = $this->statutory->firstOrNewField($request->only(array_keys($rules2)), ['employee_id']);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		//return $s;

		DB::commit();
    if ($request->input('_submit')==='next')
    	return redirect('/hr/masterfiles/employee/'.$new->lid().'/edit/personal');
  	else
    	return redirect('/hr/masterfiles/employee/'.$new->lid())->with('alert-success','Record saved!');
	}

	private function process_personal(Request $request) {

		$rules = [
      'address' 		=> 'required|max:120',
      'mobile' 			=> 'max:20',
      'phone' 			=> 'max:20',
      'fax' 				=> 'max:20',
      'birthdate' 	=> 'required|date_format:Y-m-d',
      'birthplace'  => 'max:30',
      'gender' 			=> 'required|regex:/^\d{1,3}$/',
      'civstatus' 	=> 'regex:/^\d{1,3}$/',
      'religionid'  => 'max:32|alpha_num',
      'height' 			=> 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/',
      'weight' 			=> 'regex:/^(?!$)(?:[0-9]\d{0,5})?(?:\.\d{1,2})?$/',
      'notes' 			=> 'max:225',
      'hobby'  			=> 'max:50',
      'id' 					=> 'required|max:32|alpha_num',
    ];

    if ($request->has('email'))
    	$rules['email'] = 'email|max:80';
    else
    	$rules['email'] = 'max:80';

		$this->clean_request_to_number($request, ['mobile', 'phone', 'fax']);
		$this->validate($request, $rules);

    if ($request->has('weight') && $request->input('weight')>='90')
    	$request->merge(['weight'=>($request->input('weight')*0.45359237)]);

		$new = $this->update_record($request, $rules);

    if ($request->input('_submit')==='next')
    	return redirect('/hr/masterfiles/employee/'.$new->lid().'/edit/family');
  	else
    	return redirect('/hr/masterfiles/employee/'.$new->lid().'?tab=personal')->with('alert-success','Record saved!');
	}


	private function process_family(Request $request) {

		$rules = [
      'spouse.middlename' 	=> 'max:30',
      'spouse.address' 			=> 'max:120',
      'spouse.gender' 			=> 'regex:/^\d{1,3}$/',
      'spouse.birthdate' 		=> 'date_format:Y-m-d',
      'spouse.mobile' 			=> 'max:20',
      'spouse.phone' 				=> 'max:20',
      'spouse.id' 					=> 'max:32|alpha_num',
      'ecperson.middlename' => 'max:30',
      'ecperson.address' 		=> 'max:120',
      'ecperson.relation' 	=> 'max:50',
      'ecperson.mobile' 		=> 'max:20',
      'ecperson.phone' 			=> 'max:20',
      'ecperson.id' 				=> 'max:32|alpha_num',
      'id' 									=> 'required|max:32|alpha_num',
    ];

    if ($request->has('spouse.email'))
    	$rules['spouse.email'] = 'email|max:80';
    else
    	$rules['spouse.email'] = 'max:80';

    if ($request->has('spouse.lastname') || $request->has('spouse.firstname')) {
    	$rules['spouse.lastname'] = 'required|max:30';
    	$rules['spouse.firstname'] = 'required|max:30';
    	$rules['spouse.birthdate'] = 'date_format:Y-m-d';
    } else {
    	$rules['spouse.lastname'] = 'max:30';
    	$rules['spouse.firstname'] = 'max:30';
    	$rules['spouse.birthdate'] = 'max:10';
    }

     if ($request->has('ecperson.email'))
    	$rules['ecperson.email'] = 'email|max:80';
    else
    	$rules['ecperson.email'] = 'max:80';

    if ($request->has('spouse.lastname') || $request->has('spouse.firstname')) {
    	$rules['ecperson.lastname'] = 'required|max:30';
    	$rules['ecperson.firstname'] = 'required|max:30';
    } else {
    	$rules['ecperson.lastname'] = 'max:30';
    	$rules['ecperson.firstname'] = 'max:30';
    }


    if (count($request->input('children'))>0) { 
    	foreach($request->input('children') as $key => $child) {
		    $rules['children.'.$key.'.lastname'] 		= 'required|max:30';
		    $rules['children.'.$key.'.firstname'] 	= 'required|max:30';
		    $rules['children.'.$key.'.middlename'] 	= 'max:30';
		    $rules['children.'.$key.'.birthdate'] 	= 'date_format:Y-m-d';
		    $rules['children.'.$key.'.gender'] 			= 'regex:/^\d{1,3}$/';
		    $rules['children.'.$key.'.acadlvlid']		= 'max:32|alpha_num';
		    $rules['children.'.$key.'.id'] 					= 'max:32|alpha_num';
		  }
    }

    $this->clean_request_to_number($request, ['spouse'=>['mobile', 'phone'], 'ecperson'=>['mobile', 'phone']]);
		$this->validate($request, $rules);

		$to_update = ['ecperson', 'spouse', 'children'];

		if ((!$request->has('ecperson.lastname') || !$request->has('ecperson.firstname')) && !$request->has('ecperson.id'))
				unset($to_update[0]);

		if ((!$request->has('spouse.lastname') || !$request->has('spouse.firstname')) && !$request->has('spouse.id'))
				unset($to_update[1]);

		if (!$request->has('children'))
				unset($to_update[2]);

		DB::beginTransaction();
		try {
    	$new = $this->update_child($request, $to_update, $rules);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}
		DB::commit();

    if ($request->input('_submit')==='next')
    	return redirect('/hr/masterfiles/employee/'.$new->lid().'/edit/workedu');
  	else
    	return redirect('/hr/masterfiles/employee/'.$new->lid().'?tab=family')->with('alert-success','Record saved!');
	}


	private function validateToRepository($table) {

		$model = 'App\\Models\\Boss\\'.ucwords($table);

		if(class_exists($model)) {
			$repo = 'App\\Repositories\\Boss\\'.ucwords($table).'Repository';
		
			if(!class_exists($repo))
  			throw new Exception($repo.' not found.');
		} else {

			$model2 = 'App\\Models\\'.ucwords($table);
			
			if(!class_exists($model2))
  			throw new Exception($model2.' not found.');

	  		$repo = 'App\\Repositories\\'.ucwords($table).'Repository';
			
				if(!class_exists($repo))
	  			throw new Exception($repo.' not found.');
		}
  	return app()->make($repo);
	}

	private function update_child(Request $request, $children, $rules) {
		$o = $this->employee->find($request->input('id'), ['id']);
		if (is_null($o))
			return redirect()->back()->withErrors('Employee not found.');

		if (is_array($children)) {
			foreach ($children as $table) {
				$this->saveChild($table, $request->input($table), $o->id);
			}
		}

		return $o;
	}

	private function saveChild($table, array $attrs, $id) {
		$repo = $this->validateToRepository($table);

		if (in_array($table, ['spouse', 'ecperson'])) {
			$attrs['employeeid'] = $id;
			foreach ($attrs as $k => $v)
				if (empty($v))
					unset($attrs[$k]);
			if (!array_key_exists('id', $attrs))
				$attrs['id'] = Employee::get_uid();

			$repo->firstOrNewField($attrs, ['employeeid', 'id']);
			
		} else {
			foreach ($attrs as $key => $value) {
				$value['employeeid'] = $id;
				foreach ($value as $k => $v)
					if (empty($v))
						unset($value[$k]);
				if (!array_key_exists('id', $value))
					$value['id'] = Employee::get_uid();
				$repo->firstOrNewField($value, ['employeeid', 'id']);
			}
		}
		
	}


	


	private function update_record(Request $request, $rules) {
		$o = $this->employee->find($request->input('id'));
		if (is_null($o))
			return redirect()->back()->withErrors('Employee not found.');

		$keys = array_keys($rules);
		unset($rules['id']);

		DB::beginTransaction();

		try {
    	$new = $this->employee->update($request->only($keys), $o->id);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}
		DB::commit();

		return $new;
	}


	public function deleteChild(Request $request) {

		$rules = [
      'employeeid' 	=> 'required|max:32|alpha_num',
      'table' 			=> 'alpha_num',
      'id' 					=> 'required|max:32|alpha_num',
    ];

		$this->validate($request, $rules);

		$o = $this->employee->find($request->input('employeeid'));
		if (is_null($o))
			return redirect()->back()->withErrors('Employee not found.');

		DB::beginTransaction();

		$model = '\App\Models\\'.ucfirst($request->input('table'));

		try {
    	$child = $model::where('id', $request->input('id'))
    																->where('employeeid', $request->input('employeeid'))
    																->delete();
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}
		DB::commit();

		return redirect()->back()->with('alert-success', 'Deleted!');
	}



	public function process_workedu(Request $request) {

		$rules = [
      'id' => 'required|max:32|alpha_num',
    ];

    if (count($request->input('workexp'))>0) { 
    	foreach($request->input('workexp') as $key => $child) {
		    $rules['workexp.'.$key.'.company']  		= 'required|max:50';
		    $rules['workexp.'.$key.'.position'] 		= 'required|max:50';
		    $rules['workexp.'.$key.'.periodfrom'] 	= 'date_format:Y-m';
		    $rules['workexp.'.$key.'.periodto'] 		= 'date_format:Y-m';
		    $rules['workexp.'.$key.'.remarks'] 			= 'max:150';
		    $rules['workexp.'.$key.'.id'] 					= 'max:32|alpha_num';
		  }
    }

    if (count($request->input('education'))>0) { 
    	foreach($request->input('education') as $key => $child) {
		    $rules['education.'.$key.'.school']  			= 'required|max:50';
		    $rules['education.'.$key.'.course'] 			= 'max:50';
		    $rules['education.'.$key.'.periodfrom'] 	= 'date_format:Y-m';
		    $rules['education.'.$key.'.periodto'] 		= 'date_format:Y-m';
		    $rules['education.'.$key.'.remarks'] 			= 'max:150';
		    $rules['education.'.$key.'.acadlvlid'] 		= 'required|max:32|alpha_num';
		    $rules['education.'.$key.'.id'] 					= 'max:32|alpha_num';
		  }
    }
	
    $this->validate($request, $rules);

    $o = $this->employee->find($request->input('id'));
		if (is_null($o))
			return redirect()->back()->withErrors('Employee not found.');
		
		$to_update = ['workexp', 'education'];

		if (!$request->has('workexp'))
			unset($to_update[0]);
		if (!$request->has('education'))
			unset($to_update[1]);

		DB::beginTransaction();
		try {
    	$new = $this->update_child($request, $to_update, $rules);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}
		DB::commit();

    if ($request->input('_submit')==='next')
    	return redirect('/hr/masterfiles/employee/'.$new->lid().'/edit/confirm');
  	else
    	return redirect('/hr/masterfiles/employee/'.$new->lid().'?tab=workedu')->with('alert-success','Record saved!');
	}


	public function process_confirm(Request $request) {

		$rules = [
      'id' 				=> 'required|max:32|alpha_num',
      'generate' 	=> 'boolean',
      'email' 		=> 'boolean',
      'message' 	=> 'max:1000',
    ];

    $this->validate($request, $rules);

    $o = $this->employee->find($request->input('id'));
		if (is_null($o))
			return redirect()->back()->withErrors('Employee not found.');
		if ($o->isConfirm() || $o->hasEmpfile('MAS'))
			return redirect()->back()->withErrors('Employee already confirm or has .MAS file.');

    $empCtrl = new EmpCtrl($this->employee);
    $dest = config('giligans.path.files.'.app()->environment()).'EMPFILE'.DS.'MAS';

    try {
    	$res = $empCtrl->exportByManNo(pad($o->code,6), 'MAS', $dest);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		$filename = $o->code.'.MAS';
		DB::beginTransaction();
		try {
    	$fileupload = $this->createFileUpload($res, $request, $filename, '11E88E5614DDA9E4EAAFC0F93334B77D', $o->branchid);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		try {
    	$empfile = $this->createEmpfile($o->id, $fileupload);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		$this->employee->update(['processing'=>1], $o->id);
		
		DB::commit();

		//return $o->branch->email;

		$email_add = !is_null($o->branch->email) ?: 'jefferson.salunga@yahoo.com';

		
		try {
			$this->email($email_add, $o->branch->code, $o->code, $o->firstname.' '.$o->lastname, $fileupload, $dest.DS.$filename);
    } catch (Exception $e) {
			return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
    
    return redirect()->back()->with('alert-success', 'Employee has been confirmed and generated .MAS file.');
  }


  private function createFileUpload($src, Request $request, $filename, $doctypeid, $branchid){

  	$fileUploadRepo = new \App\Repositories\FileUploadRepository(app());

	 	$data = [
	 		'branch_id' 		=> $branchid,
    	'filename' 			=> $filename,
    	'year' 					=> c()->format('Y'), //$request->input('year'),
    	'month' 				=> c()->format('m'), //$request->input('month'),
    	'size' 					=> filesize ($src),
    	'mimetype' 			=> 'application/mas',
    	'filetype_id'		=> $doctypeid,
    	'terminal' 			=> clientIP(), //$request->ip(),
    	'user_remarks' 	=> $request->input('message'),
    	'user_id' 			=> $request->user()->id,
    	'cashier' 			=> $request->user()->name,
    	'updated_at' 		=> c()
    ];

    return $fileUploadRepo->create($data)?:NULL;
  }

  private function email($to, $brcode, $man_no, $name, $fileupload, $filepath) {
		
		$data = [
			'to' 					=> $to,
			'branchcode' 	=> $brcode,
			'man_no' 			=> $man_no,
			'name' 				=> $name,
			'attachment' 	=> $filepath,
			'user'				=> 'Giligans HRIS',
			//'cashier'			=> $fileupload->cashier,
			'cashier'			=> '',
			'filename'		=> $fileupload->filename,
			'remarks'			=> $fileupload->user_remarks,
			'email'				=> request()->user()->email
		];
		
		try {

			Mail::queue('emails.hris.man_no', $data, function ($message) use ($data) {
	        $message->subject('Man# '.$data['man_no'].' '.$data['name'].' ('.$data['branchcode'].')');
	        $message->from('giligans.app@gmail.com', 'Giligans HRIS');
	       	//$message->to('giligans.app@gmail.com');
	       	$message->to($data['to']);
	       	$message->cc('giligans.hris@gmail.com');
	       	$message->replyTo($data['email'], $data['user']);

	        //if (app()->environment()==='production')
	        	//$message->to('gi.hrd01@gmail.com');
	       	
	       	$message->attach($data['attachment']);
	    });

		} catch (Exception $e) {
			throw $e;
			return false;
		}
		return true;
	}

  private function createEmpfile($employeeid, $fileupload) {

  	$data = [
	 		'branch_id' 		=> $fileupload->branch_id,
	 		'employee_id' 	=> $employeeid,
    	'filename' 			=> $fileupload->filename,
    	'type' 					=> 1,
    	'file_upload_id'=> $fileupload->id,
    	'remarks'				=> $fileupload->user_remarks,
    	'updated_at' 		=> c()
    ];

    return \App\Models\Empfile::create($data);
  }





	public function edit(Request $request, $id) {
		$employee = $this->employee->codeID($id, ['code', 'lastname', 'firstname', 'middlename', 'id']);

		if ($request->has('raw') && $request->input('raw')=='data')
			return $employee;

		return is_null($employee)
			? abort('404')
			: view('hr.masterfiles.employee.edit')
						->with('employee', $employee);	
	}

	public function editEmployment(Request $request, $id) {
		$employee = $this->employee->with('statutory')->codeID($id);

		if ($request->has('raw') && $request->input('raw')=='data')
			return $employee;

		return is_null($employee)
			? abort('404')
			: view('hr.masterfiles.employee.edit-employment')
						->with('employee', $employee)
						->with('departments', $this->department->orderBy('descriptor')->all())
						->with('companies', $this->company->orderBy('code')->all())
						->with('branches', $this->branch->orderBy('code')->all())
						->with('positions', $this->position->orderBy('descriptor')->all());
	}

	public function editPersonal(Request $request, $id) {
		$employee = $this->employee->codeID($id);

		if ($request->has('raw') && $request->input('raw')=='data')
			return $employee;

		return is_null($employee)
			? abort('404')
			: view('hr.masterfiles.employee.edit-personal')
						->with('employee', $employee)
						->with('religions', $this->religion->all());
	}

	public function editFamily(Request $request, $id) {
		$employee = $this->employee->with(['childrens', 'spouse', 'ecperson'])->codeID($id);

		if ($request->has('raw') && $request->input('raw')=='data')
			return $employee;

		return is_null($employee)
			? abort('404')
			: view('hr.masterfiles.employee.edit-family')
						->with('employee', $employee)
						->with('acadlevels', \App\Models\Acadlevel::all());
		
	}

	public function editWorkedu(Request $request, $id) {
		$employee = $this->employee->with(['workexps', 'educations'])->codeID($id);

		if ($request->has('raw') && $request->input('raw')=='data')
			return $employee;

		return is_null($employee)
			? abort('404')
			: view('hr.masterfiles.employee.edit-workedu')
						->with('employee', $employee)
						->with('acadlevels', \App\Models\Acadlevel::all());
		
	}

	public function editConfirm(Request $request, $id) {

		$employee = $this->employee->with(['empfile', 'branch', 'statutory'])->codeID($id);

		if ($request->has('raw') && $request->input('raw')=='data')
			return $employee;

		//return dd($employee);

		$valid = true;
		$invalid_fields = [];
		
		$rules = [
      'id' 				=> ['desc'=>'ID', 'url'=>''],
      'lastname' 	=> ['desc'=>'Lastname', 'url'=>'edit'],
      'firstname'	=> ['desc'=>'Firstname', 'url'=>'edit'],
      'companyid'	=> ['desc'=>'Company', 'url'=>'edit'],
      'branchid'	=> ['desc'=>'Branch', 'url'=>'edit'],
      'deptid'		=> ['desc'=>'Department', 'url'=>'edit/employment'],
      'positionid'=> ['desc'=>'Position', 'url'=>'edit/employment'],
      'paytype'		=> ['desc'=>'Pay Type', 'url'=>'edit/employment'],
      'ratetype'	=> ['desc'=>'Rate Type', 'url'=>'edit/employment'],
      'datestart'	=> ['desc'=>'Date Start', 'url'=>'edit/employment'],
      'rate'			=> ['desc'=>'Rate', 'url'=>'edit/employment'],
      'sssno'			=> ['desc'=>'SSS #', 'url'=>'edit/employment'],
      'phicno'		=> ['desc'=>'PhilHealth #', 'url'=>'edit/employment'],
      'hdmfno'		=> ['desc'=>'Pag Ibig #', 'url'=>'edit/employment'],
      //'fax'				=> ['desc'=>'Fax #', 'url'=>'edit/personal'],
    ];

    foreach ($employee->toArray() as $field => $value) {
    	if (array_key_exists($field, $rules))
	    	if (empty($value)){
	    		$invalid_fields[$field] = $rules[$field];
	    		if ($valid)
	   				$valid = false;
	    	}    	
    }

		return is_null($employee)
			? abort('404')
			: view('hr.masterfiles.employee.edit-confirm')
						->with('valid', $valid)
						->with('invalid_fields', $invalid_fields)
						->with('employee', $employee);
		
	}








	private function clean_request_number_format($request, $arr) {
		if (is_array($arr)) 
			foreach ($arr as $key => $value)
				$request->merge([$value => clean_number_format($request->input($value))]);
	}


	private function clean_request_govmt($request, $arr) {
		if (is_array($arr)) 
			foreach ($arr as $key => $value)
				$request->merge([$value => str_replace('-', '', $request->input($value))]);
	}

	private function clean_request_to_number($request, $arr) {
		if (is_array($arr)) 
			foreach ($arr as $key => $value)
				if (is_array($value) && $request->has($key)) {    // [spouse =>  ['mobile']]
					$table = [];
					foreach ($request->input($key) as $field => $val)
						if (in_array($field, $value) && !empty($val))
							$table[$field] = str_replace(['-', '(', ')', ' '], '', $val);
						else
							$table[$field] = $val;
					$request->merge([$key => $table]);
				} else 
					$request->merge([$value => str_replace(['-', '(', ')', ' '], '', $request->input($value))]);
	}



	public function printPreview(Request $request, $id) {
	
		$employee = $this->employee->codeID($id);

		return is_null($employee)
			? abort('404')
			: view('hr.masterfiles.employee.print-preview')
						->with('employee', $employee);
	}







	


	

  


}