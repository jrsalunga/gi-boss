<?php namespace App\Http\Controllers;

use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Validator;
use Auth;
use App\Events\UserChangePassword;
use App\Models\Branch;
use App\Models\BossBranch;
use App\Repositories\BossBranchRepository;
use App\Repositories\BranchRepository as BranchRepo;
use App\Repositories\Criterias\BossBranchCriteria;
use Carbon\Carbon;

class SettingsController extends Controller {

	protected $repository;

	public function __construct(BossBranchRepository $repository, BranchRepo $branch) {
		$this->repository = $repository;
		$this->repository->pushCriteria(new BossBranchCriteria);
		$this->branch = $branch;
	}



	public function getIndex(Request $request, $param1=null, $param2=null){
		if(strtolower($param1)==='add')
			return $this->makeAddView($request);
		else if(preg_match('/(20[0-9][0-9])/', $param1) && (strtolower($param2)==='week') && preg_match('/^[0-9]+$/', $param3)) //((strtolower($param1)==='week') && preg_match('/^[0-9]+$/', $param2)) 
			return $this->makeViewWeek($request, $param1, $param3); //task/mansked/2016/week/7
		else if(preg_match('/^[A-Fa-f0-9]{32}+$/', $param1) && strtolower($param2)==='edit')
			return $this->makeEditView($request, $param1);
		else if($param1==='bossbranch' && $param2==null)   //preg_match('/^[A-Fa-f0-9]{32}+$/',$action))
			return $this->makeBossbranchView($request, $param1, $param2);
		else if($param1==='password' && $param2==null)   //preg_match('/^[A-Fa-f0-9]{32}+$/',$action))
			return $this->makePasswordView($request, $param1, $param2);
		else
			return $this->makeIndexView($request, $param1, $param2);
	}



	public function makeIndexView(Request $request, $p1, $p2) {

		$user = User::where('id', $request->user()->id)
					->first();
	
		return view('settings.index')->with('user', $user);	
	}

	public function makePasswordView(Request $request, $p1, $p2) {

	
		return view('settings.password');	
	}

	public function changePassword(Request $request) {

		$rules = array(
			'passwordo'      => 'required|max:50',
			'password'      	=> 'required|confirmed|max:50|min:8',
			'password_confirmation' => 'required|max:50|min:8',
		);

		$messages = [
	    'passwordo.required' => 'Old password is required.',
	    'password.required' => 'New password is required.',
		];
		
		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails())
			return redirect('/settings/password')->withErrors($validator);

		if (!Auth::attempt(['username'=>$request->user()->username, 'password'=>$request->input('passwordo')]))
			return redirect('/settings/password')->withErrors(['message'=>'Invalid old password.']);

		$user = User::find($request->user()->id)
								->update(['password' => bcrypt($request->input('password'))]);
		
		if(!$user)
			return redirect('/settings/password')->withErrors(['message'=>'Unable to change password.']);
		
		event(new UserChangePassword($request));

		return redirect('/settings/password')->with('alert-success', 'Password changed!');
		return view('settings.password');	
	}



	public function makeBossbranchView(Request $request, $p1, $p2) {

		$arr = [];

		$bb = $this->repository->skipCache()->all(['branchid']);
		//return $bb;
		
		$branchs = $this->branch->all(['code', 'descriptor', 'id']);
		$i = 0;
		foreach ($branchs as $branch) {
			$b = $bb->where('branchid', $branch->id)->first();
			$arr[$i]['branch'] = $branch;
			$arr[$i]['assign'] = is_null($b) ? null : $branch->id;
			$i++;
		}
		
		
		
		

		//return $arr;
		
		return view('settings.bossbranch')->with('datas', $arr);	
	}


	public function assignBranch(Request $request){


		if($request->ajax()){
			$b = BossBranch::where('branchid', $request->input('branchid'))
								->where('bossid', $request->user()->id) 
								->first();
				// false = create & null
			if ($request->input('assign') && is_null($b)) 
				//$this->repository->create()
				BossBranch::create(['branchid' => $request->input('branchid'), 'bossid' => $request->user()->id]);
			//elseif (!$request->input('assign') && !is_null($b)) // true = delete & not null
			//	echo ''; //$b->delete();
			else 
				$b->delete();
		}

		return json_encode(['branch'=>$b, 'assign'=>$request->input('assign')]);
	}




	public function empImport(Request $request) {
	
		return view('settings.emp-import');	
	}

	public function postEmpImport(Request $request) {

		
		if (!$request->hasFile('empfile'))
			return back()->withErrors('No .EMP file attached!');

		if (!$request->file('empfile')->isValid())
			return back()->withErrors('.EMP file is corrupted! Please re-upload again.');	

		$ext = $request->file('empfile')->getClientOriginalExtension();
		$filename = substr($request->file('empfile')->getClientOriginalName(), 0, -4);

		if (strtoupper($ext)!=='MAS')
			return back()->withErrors('Invalid file!');	

		try {
			$request->file('empfile')->move(storage_path().DS.'mas', $filename.'.DBF');
		} catch (\Exception $e) {
			return back()->withErrors('Error: '.$e->getMessage());
		}

		$dbf_path = storage_path().DS.'mas'.DS.$filename.'.DBF';



		DB::beginTransaction();
		try {
			$employee = $this->importMAS($dbf_path, $filename);
		} catch (\Exception $e) {
			DB::rollBack();
			return back()->withErrors('Error Importing: '.$e->getMessage());
		}
		DB::commit();
		
		return back()->with('alert-success', $employee->code.' '.$employee->lastname.', '.$employee->firstname.' imported to '.$employee->branch->code.'!')
			->with('alert-important', '');
		return redirect('/employee/'.$employee->lid());
	
	
	}



	public function importMAS($dbf, $filename){

		$logfile = storage_path().DS.'mas'.DS.$filename.'.txt';

		$log = false;
		$import = true;

		if ($import)
			$db = dbase_open($dbf, 0);
		else
			$db = dbase_open($dbf, 0);
		


		if ($db) {

			$header = dbase_get_header_info($db);
			
			if(!$import)
			echo '<table cellpadding="2" cellspacing="0" border="1"><thead>';

			// render table header
			if(!$import) {
				echo '<tr>';
				echo '<th>Exist?</th>';
				foreach ($header as $key => $value) {
				echo '<th>'.$value['name'].'</th>';
				}
				echo '</tr>';
			}
			
			
		 	// render table body
		 	$exist_emp = false;
		 	$children_ctr = 0;
		 	$ecperson_ctr = 0;
		 	$education_ctr = 0;
		 	$work_ctr = 0;
		 	$spouse_ctr = 0;
		 	$record_numbers = dbase_numrecords($db);
		  for($i = 1; $i <= $record_numbers; $i++) {

		    $row = dbase_get_record_with_names($db, $i);
		  	if($i==1)
		  		$brcode = trim($row['BRANCH']);

		  	
		  	$return = null;
		  	$e = \App\Models\Employee::where('code', trim($row['MAN_NO']))->first();

		  	if((!$e)) {
		  		$employee = new \App\Models\Employee;
		  		$employee->id = $employee->get_uid();

		  		$employee->code 				= trim($row['MAN_NO']);
			    $employee->lastname 		= trim($row['LAST_NAM']);
			    $employee->firstname		= trim($row['FIRS_NAM']);
			    $employee->middlename		= trim($row['MIDL_NAM']);
		  		
		  		$exist_emp = false;

		  		$return = $employee;
		  		//$exist_emp = true;
		  	} else {
		  		$employee = \App\Models\Employee::find($e->id);
		  		$exist_emp = true;

		  		$return = $employee;
		  	}


		    
		    $employee->companyid		= trim($this->getCompanyId($row['CO_NAME']));
		    
		   	$branch 								= \App\Models\Branch::where('code', trim($row['BRANCH']))->first();
		    $employee->branchid			= is_null($branch) ? '': $branch->id;
		    $employee->deptid				= $this->getDeptId($row['DEPT']);
		    $employee->positionid		= $this->getPositionId(trim($row['POSITION']));
		    $employee->paytype			= 2;
		    $employee->ratetype			= 2;
		    $employee->rate					= trim($row['RATE_HR']);
		    $employee->ecola				= trim($row['RATE_HR']);
		    $employee->allowance1		= trim($row['ALW1_RATE']);
		    $employee->allowance2		= trim($row['ALW2_RATE']);
		    $employee->phicno				= trim($row['PHEALTH_NO']);
		    $employee->hdmfno				= trim($row['PBIG_NO']);
		    $employee->tin 					= trim($row['WTAX_NO']);
		    $employee->sssno 				= trim($row['SSS_NO']);
		    $employee->empstatus		= $this->getEmpstatus(trim($row['EMP_STUS']));
		    $employee->datestart		= Carbon::parse(trim($row['STARTED']));
		    $hired = empty(trim($row['HIRED'])) ? '0000-00-00' : Carbon::parse(trim($row['HIRED']));
		    $employee->datehired		= $hired;
		    $stop = empty(trim($row['RESIGNED'])) ? '0000-00-00' : Carbon::parse(trim($row['RESIGNED']));
		    $employee->datestop			= $stop;
		    $employee->punching			= 1;
		    $employee->processing		= 1;
		    $employee->address			= trim($row['ADDRESS1']).', '.trim($row['ADDRESS2']).', '.trim($row['ADDRESS3']);
		    $employee->phone 				= trim($row['TEL']);
		    //$employee->fax 					= trim($row['TEL']);
		    $employee->mobile 			= trim($row['CEL']);
		    $employee->email 				= trim($row['EMAIL']);
		    $employee->gender 			= trim($row['SEX'])=='M' ? 1:2;
		    $employee->civstatus 		= trim($row['CIV_STUS'])=='SINGLE' ? 1:2;
		    $employee->height 			= str_replace("'",'.',trim($row['HEIGHT']));
		    $employee->weight 			= trim($row['WEIGHT']);
		    $employee->birthdate		= Carbon::parse(trim($row['BIRTHDATE']));
		    $employee->birthplace		= trim($row['BIRTHPLC']);
		    $employee->religionid		= trim($this->getReligionId($row['RELIGION']));
		    $employee->hobby				= trim($row['HOBBIES']);
		    $employee->notes				= 'UNIFORM:'.trim($row['UNIFORM']).'; '.
		    													'SP_NOTES1:'.trim($row['SP_NOTES1']).'; '.
		    													'SP_NOTES2:'.trim($row['SP_NOTES2']).'; ';

		    //if($import && !$exist_emp)
		    if($import)
		     	$employee->save();
		    
		    if(!$exist_emp) {

			    $childrens = [];
			    if(!empty(trim($row['CHILDREN1'])) && trim($row['CHILDREN1'])!='N/A') {
			    	$c1 = new \App\Models\Children;
			    	$c1->firstname = trim($row['CHILDREN1']);
			    	$c1->lastname = $employee->lastname;
			    	$c1->id = $c1->get_uid();
			    	array_push($childrens, $c1);
			    	$children_ctr++;
			    }

			    if(!empty(trim($row['CHILDREN2'])) && trim($row['CHILDREN2'])!='N/A') {
			    	$c2 = new \App\Models\Children;
			    	$c2->firstname = trim($row['CHILDREN2']);
			    	$c2->lastname = $employee->lastname;
			    	$c2->id = $c2->get_uid();
			    	array_push($childrens, $c2);
			    	$children_ctr++;
			    }

			    if($import)
			    	$employee->childrens()->saveMany($childrens);



			    if(!empty(trim($row['EMER_NAM'])) && trim($row['EMER_NAM'])!='N/A') {
			    	$emer = explode(' ', trim($row['EMER_NAM']));
			    	$e = new \App\Models\Ecperson;
			    	$e->firstname = empty($emer[0])?'':$emer[0];
			    	$e->lastname = empty($emer[1])?'':$emer[1];
			    	$e->mobile = trim($row['EMER_NO']);
			    	$e->id = $e->get_uid();
			    	$ecperson_ctr++;
			    	if($import)
			    		$employee->ecperson()->save($e);	
			    }


			    if(!empty(trim($row['EDUCATION'])) && trim($row['EDUCATION'])!='N/A') {
			    	$edu = new \App\Models\Education;
			    	$edu->school = trim($row['EDUCATION']);
			    	$edu->id = $edu->get_uid();
			    	

			    	if($import)
			    		$employee->educations()->saveMany([$edu]);	
			    	$education_ctr++;
			    }
			    

			    $works = [];
			    if(!empty(trim($row['WORKHIST1'])) && trim($row['WORKHIST1'])!='N/A') {
			    	$w1 = new \App\Models\Workexp;
			    	$w1->company = trim($row['WORKHIST1']);
			    	$w1->id = $w1->get_uid();
			    	array_push($works, $w1);
			    	$work_ctr++;
			    }

			    if(!empty(trim($row['WORKHIST2'])) && trim($row['WORKHIST2'])!='N/A') {
			    	$w2 = new \App\Models\Workexp;
			    	$w2->company = trim($row['WORKHIST2']);
			    	$w2->id = $w2->get_uid();
			    	array_push($works, $w2);
			    	$work_ctr++;
			    }

			    if(!empty(trim($row['WORKHIST3'])) && trim($row['WORKHIST3'])!='N/A') {
			    	$w3 = new \App\Models\Workexp;
			    	$w3->company = trim($row['WORKHIST3']);
			    	$w3->id = $w3->get_uid();
			    	array_push($works, $w3);
			    	$work_ctr++;
			    }

			    if(!empty(trim($row['WORKHIST4'])) && trim($row['WORKHIST4'])!='N/A') {
			    	$w4= new \App\Models\Workexp;
			    	$w4->company = trim($row['WORKHIST2']);
			    	$w4->id = $w4->get_uid();
			    	array_push($works, $w4);
			    	$work_ctr++;
			    }

			    if($import)
			    	$employee->workexps()->saveMany($works);


			    if(!empty(trim($row['SPOUS_NAM'])) && trim($row['SPOUS_NAM'])!='N/A' && trim($row['SPOUS_NAM'])!='NA/A' ) {
			    	$sp = preg_split("/\s+(?=\S*+$)/", trim($row['SPOUS_NAM']));
			    	$spou = new \App\Models\Spouse;
			    	$spou->firstname = empty($sp[0])?'':$sp[0];
			    	$spou->lastname = empty($sp[1])?'':$sp[1];
			    	$spou->id = $spou->get_uid();
			    	$spouse_ctr++;
			    
			    if($import)
			    		$employee->spouse()->save($spou);	
			    }

		    }
		    
				

		    

		   	if(!$import) {
			    echo '<tr>';
			    echo '<td>'.$exist_emp.'</td>';
					foreach ($header as $key => $value) {
						//if($value['name']=='CO_NAME')
							//echo '<td>'.$this->getCompanyId($row[$value['name']]).'</td>';
						//else
							echo '<td>'.$row[$value['name']].'</td>';
					}
					echo '</tr>';
		   	}
		 }

		if($import && $log) {
			echo $brcode.' imported! </br>';
			$handle = fopen($logfile, 'a');
			$content = $brcode."\n\temployee:\t\t". $record_numbers ."\n";
			$content .= "\tspouse:\t\t\t". $spouse_ctr ."\n";
			$content .= "\tchildren:\t\t". $children_ctr ."\n";
			$content .= "\tecperson:\t\t". $ecperson_ctr ."\n";
			$content .= "\tworkexp:\t\t". $work_ctr ."\n";
	    fwrite($handle, $content);
	    fclose($handle);
		}



			dbase_close($db);
		}

		return $return;
	}


	public function getEmpstatus($c){
		 
		switch (trim($c)) {
			case "CONTRACT":
				return 2;
				break;
			case "TRAINEE":
				return 0;
				break;
			case "TRAINEE 1":
				return 0;
				break;
			case "TEMPORARY":
				return 1;
				break;
			case "TEMPO":
				return 1;
				break;
			case "REGULAR":
				return 3;
				break;
			default:
				return '';
				break;
		}
	}



	public function getCompanyId($c){
		 
		switch (trim($c)) {
			case "ALQUIROS FOOD CORP.":
				return '29E4E2FA672C11E596ECDA40B3C0AA12';
				break;
			case "GILIGAN'S ISLAND BAGUIO, INC.":
				return '43400E83673811E596ECDA40B3C0AA12';
				break;
			case "IONE-6 FOODS":
				return '6A2F5687673611E596ECDA40B3C0AA12';
				break;
			case "SHA-DINE-6 DINERS":
				return '81D62659673611E596ECDA40B3C0AA12';
				break;
			case "FIJON-6 FOODS":
				return '43B6B571673611E596ECDA40B3C0AA12';
				break;
			case "ROSE FOUR DINERS":
				return '7E8F8AC3673611E596ECDA40B3C0AA12';
				break;
			case "NATHANAEL-6 FOODS":
				return '70F73EAD673611E596ECDA40B3C0AA12';
				break;
			case "FILBERT'S-6 FOODS":
				return '57F10712673611E596ECDA40B3C0AA12';
				break;
			case "FJN6 FOOD CORPORATION":
				return '5C010584673611E596ECDA40B3C0AA12';
				break;
			case "KAWBINADIT CORP.":
				return '7A859059673611E596ECDA40B3C0AA12';
				break;
			case "NIKDER SIX FOODS":
				return '74B1CBDC673611E596ECDA40B3C0AA12';
				break;
			case "FJN6 FOOD CORP.":
				return '5C010584673611E596ECDA40B3C0AA12';
				break;
			case "NEILZACH RESTAURANT":
				return 'DB02D166D56A466D9804BEFD3589E432';
				break;
			default:
				return '';
				break;
		}
	}

	public function getDeptId($dept){

		if(starts_with($dept, 'KIT'))
			return '71B0A2D2674011E596ECDA40B3C0AA12';
		if(starts_with($dept, 'DIN'))
			return '75B34178674011E596ECDA40B3C0AA12';
		if(starts_with($dept, 'OPS'))
			return '201E68D4674111E596ECDA40B3C0AA12';
		if(starts_with($dept, 'CSH'))
			return 'DC60EC42B0B143AFA7D42312DA5D80BF';
		if(starts_with($dept, 'ADM'))
			return 'D2E8E339A47B11E592E000FF59FBB323';
		return '';	
	
	}


	public function getReligionId($c){
		 
		switch (trim($c)) {
			case "R.CATH":
				return '1A95F32E674811E596ECDA40B3C0AA12';
				break;
			case "R. CATH":
				return '1A95F32E674811E596ECDA40B3C0AA12';
				break;
			case "R.CATH,":
				return '1A95F32E674811E596ECDA40B3C0AA12';
				break;
			case "R.CATH,":
				return '1A95F32E674811E596ECDA40B3C0AA12';
				break;
			case "R,CATH":
				return '1A95F32E674811E596ECDA40B3C0AA12';
				break;
			case "R'CATHOLIC":
				return '1A95F32E674811E596ECDA40B3C0AA12';
				break;
			case "R.CATH.":
				return '1A95F32E674811E596ECDA40B3C0AA12';
				break;
			case "MARRIED":
				return '1A95F32E674811E596ECDA40B3C0AA12';
				break;
			case "CATHOLIC":
				return '1A95F32E674811E596ECDA40B3C0AA12';
				break;
			case "CATH":
				return '1A95F32E674811E596ECDA40B3C0AA12';
				break;
			case "CAM. SUR":
				return '1A95F32E674811E596ECDA40B3C0AA12';
				break;
			case "CHRISTIAN":
				return '2975665F674811E596ECDA40B3C0AA12';
				break;
			case "JEHOVA":
				return '465B9151A30E11E592E000FF59FBB323';
				break;
			case "JEHOVA'S":
				return '465B9151A30E11E592E000FF59FBB323';
				break;
			case "INC":
				return '2D6A8A3A674811E596ECDA40B3C0AA12';
				break;
			case "I.N.C.":
				return '2D6A8A3A674811E596ECDA40B3C0AA12';
				break;
			case "IGLESIA":
				return '2D6A8A3A674811E596ECDA40B3C0AA12';
				break;
			case "AGLIPAYIN":
				return '9ED09932A3D511E592E000FF59FBB323';
				break;
			case "S.D.A":
				return 'A87C6E4EA3DE11E592E000FF59FBB323';
				break;
			case "SDA":
				return 'A87C6E4EA3DE11E592E000FF59FBB323';
				break;
			case "7DAY ADVNT":
				return 'A87C6E4EA3DE11E592E000FF59FBB323';
				break;
			case "BAPTIST":
				return 'AF2E222CA3DE11E592E000FF59FBB323';
				break;
			case "BORN AGAIN":
				return '71FC2C52A3E311E592E000FF59FBB323';
				break;
			case "PROTESTANT":
				return '71FC2C52A3E311E592E000FF59FBB323';
				break;
			case "METHODIST":
				return '14D98381A47A11E592E000FF59FBB323';
				break;
			case "ALLIANCE":
				return '45942FF9A47A11E592E000FF59FBB323';
				break;
			case "L.D.SAINTS":
				return '052FE585A48011E592E000FF59FBB323';
				break;
			case "CRUSADER":
				return '0EEEE7B6A48411E592E000FF59FBB323';
				break;

				
			default:
				return '1A95F32E674811E596ECDA40B3C0AA12';
				break;
		}
	}


	public function getPositionId($pos){
		$p = \App\Models\Position::where('descriptor', $pos)->first();
		if(!is_null($p))
			return $p->id;

		switch (trim($pos)) {
			case "Dining Supv.":
				return 'B3622DDF666611E596ECDA40B3C0AA12';
				break;
			case "Dining Super":
				return 'B3622DDF666611E596ECDA40B3C0AA12';
				break;
			case "Cashier Seni":
				return '69427592A5E111E385D3C0188508F93C';
				break;
			case "Cashier Sr.":
				return '69427592A5E111E385D3C0188508F93C';
				break;
			case "Dining Asst.":
				return '8EF16963673A11E596ECDA40B3C0AA12';
				break;
			case "Kitchen Supe":
				return 'A7006EB7A3D411E592E000FF59FBB323';
				break;
			case "Kitchen Asst":
				return 'D02091AB673A11E596ECDA40B3C0AA12';
				break;
			case "OIC Kitchen":
				return '81BCB53BA3D711E592E000FF59FBB323';
				break;
			case "Kitchen Supv":
				return 'A7006EB7A3D411E592E000FF59FBB323';
				break;
			case "Manager - Op":
				return '55FC33F0A30211E592E000FF59FBB323';
				break;
			case "Mngr Branch":
				return '55FC33F0A30211E592E000FF59FBB323';
				break;
			case "Management T":
				return 'EC5ED785673A11E596ECDA40B3C0AA12';
				break;
			case "Mgmt Trainee":
				return 'EC5ED785673A11E596ECDA40B3C0AA12';
				break;
			case "Utility Staf":
				return '67B0F27F673B11E596ECDA40B3C0AA12';
				break;
			case "Tech'n":
				return 'F55DA154A47B11E592E000FF59FBB323';
				break;
			case "Tech'n Sr.":
				return '553820C0A47C11E592E000FF59FBB323';
				break;
			case "TRAINEE":
				return 'E16F473C86A94EF09C658286BEDEF89A';
				break;
			case "TRAINEE 1":
				return '292FC22C808C11E6B7C800FF18C615EC';
				break;
			case "TRAINEE 2":
				return '76A923D32879406E8D6D62EB6F81277B';
				break;
			case "TRAINEE 3":
				return '179E8AB1C5BD402E90E69A7F14E7F16F';
				break;
			default:
				return '';
				break;
		}

	}



	
}