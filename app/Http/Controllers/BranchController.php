<?php namespace App\Http\Controllers;

use DB;
use File;
use Exception;
use Validator;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Branch;
use App\Repositories\BranchRepository;
use App\Repositories\Boss\BranchRepository as BossBr;
use App\Repositories\DateRange;
use App\Repositories\DailySalesRepository as DSRepo;
use App\Repositories\Criterias\ActiveBranchCriteria as ActiveBranch;
use App\Repositories\Boss\CompanyRepository as CompRepo;
use App\Repositories\LessorRepository as LessorRepo;
use App\Repositories\Boss\SectorRepository as SectorRepo;



class BranchController extends Controller 
{

	protected $repository;
	protected $branchBoss;
	protected $company;
	protected $lessor;
	protected $sector;

	public function __construct(BranchRepository $branchrepository, DSRepo $dsrepo, DateRange $dr, BossBr $branchBoss, CompRepo $compRepo, LessorRepo $lessorRepo, SectorRepo $sectorRepo){

		$this->repository = $branchrepository;
		$this->repository->pushCriteria(new ActiveBranch);
		$this->dr = $dr;
		$this->ds = $dsrepo;
		$this->branchBoss = $branchBoss;
		$this->company = $compRepo;
		$this->lessor = $lessorRepo;
		$this->sector = $sectorRepo;
	}

	//status/branch/{branchid}
	public function getStatus(Request $request, $branchid = NULL) {

		$branches = $this->repository->all(['code', 'descriptor', 'id']);
		
		if (is_null($branchid)) {
			$branch = null;
			$dailysales = null;
			return $this->setViewWithDR(view('status.branch')
									->with('dailysales', $dailysales)
									->with('branches', $branches)
									->with('branch', $branch));
		}
		
		if (!is_uuid($branchid)) 
			return redirect('/status/branch')->withErrors(['message'=>'Invalid branchid']);

		$branch = $this->repository->find($branchid, ['code', 'descriptor', 'mancost', 'id']);

		$dailysales = $this->ds->branchByDR($branch, $this->dr);
		
		return $this->setViewWithDR(view('status.branch')
								->with('dailysales', $dailysales)
								->with('branches', $branches)
								->with('branch', $branch));
	}

	public function postStatus(Request $request) {

		$fr = carbonCheckorNow($request->input('fr'));
		$to = carbonCheckorNow($request->input('to'));

		if ($to->lt($fr)) {
			$to = Carbon::now();
			$fr = $to->copy()->subDay(30); //$fr = $to->copy()->subDay(7);
		} 

		$this->dr->fr = $fr;
		$this->dr->to = $to;
		$this->dr->date = $to;

		$rules = array(
			'branchid' 	=> 'required|max:32|min:32',
		);

		$messages = [
	    'branchid.required' 	=> 'Please a select Branch.',
	    'branchid.min' 				=> 'Please a select Branch..',
	    'branchid.max' 				=> 'Please a select Branch...',
		];
		
		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails())
			return redirect('/status/branch')->withErrors($validator)
							->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 120))
							->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 120))
							->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 120));

		return redirect('/status/branch/'.strtolower($request->input('branchid')))
							->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 120))
							->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 120))
							->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 120));
		
		return $request->all();
	}

	public function getComparative(Request $request) {

		$branches = $this->repository->all(['code', 'descriptor', 'mancost', 'id']);

		return $this->setViewWithDR(view('status.comparative')
								->with('branches', $branches)); 
	}

	public function postComparative(Request $request) {

		return  $request->input('branch');
		$branches = $this->repository->all(['code', 'descriptor', 'mancost', 'id']);

		return $this->setViewWithDR(view('status.comparative')
								->with('branches', $branches)); 
	}

	public function getComparativeCSV(Request $request){

		$this->dr->fr = carbonCheckorNow($request->input('fr'));
		$this->dr->to = carbonCheckorNow($request->input('to'));


		$branches = $this->repository->findWhereIn('id', $request->input('branches'), ['code', 'descriptor', 'mancost', 'id']);

		$dss = $this->ds->with(['branch'=>function($query){
						$query->select(['code', 'descriptor', 'mancost', 'id']);
					}])
					->scopeQuery(function($query) use ($request) {
             return $query->whereBetween('date', [$this->dr->fr->format('Y-m-d'), $this->dr->to->format('Y-m-d')])
              						->whereIn('branchid', $request->input('branches'))
                          ->orderBy('date', 'ASC');
          })->all();


		echo "Date";
		foreach ($branches as $b) {
			echo ','.$b->code;
		}

		echo PHP_EOL;

		
		foreach ($this->dr->dateInterval() as $date) {
			echo $date->format('Y-m-d');

			foreach ($branches as $b) {
				
				$filtered = $dss->filter(function ($item) use ($b, $date){
				  return $item->branchid == $b->id && $item->date->format('Y-m-d') == $date->format('Y-m-d') ? $item : null;
				})->first();

				$ds = $filtered;

				echo ',';

				if(is_null($ds))
					echo 0;
				else {
					
					if($request->input('stat')==2) {

						//echo number_format($ds['empcount']*$b->mancost,2,'.','');
						echo number_format($ds['mancostpct'],2,'.','');
						//echo $ds['mancostpct'];
					} elseif($request->input('stat')==3)
						echo $ds['tipspct'];
					elseif($request->input('stat')==4) 
						echo $ds->empcount=='0' ? '0.00':number_format(($ds->sales/$ds->empcount),2,'.','');
					else
						echo $ds['sales'];
				}

			}
			echo PHP_EOL;
		}


		$response = new Response;
	 	$response->header('Content-Type','text/csv');
	  return $response;
	}



	public function getComparativeJSON(Request $request){

		$this->dr->fr = carbonCheckorNow($request->input('fr'));
		$this->dr->to = carbonCheckorNow($request->input('to'));

		$arr = [];


		$branches = $this->repository->findWhereIn('id', $request->input('branches'), ['code', 'descriptor', 'mancost', 'id']);

		$dss = $this->ds->skipCache()->with(['branch'=>function($query){
						$query->select(['code', 'descriptor', 'mancost', 'id']);
					}])
					->scopeQuery(function($query) use ($request) {
             return $query->whereBetween('date', [$this->dr->fr->format('Y-m-d'), $this->dr->to->format('Y-m-d')])
              						->whereIn('branchid', $request->input('branches'))
                          ->orderBy('date', 'ASC');
          })->all();


		
		



		
		foreach ($branches as $b) {
			$arr[$b->code] = [];
			foreach ($this->dr->dateInterval() as $date) {
				
				$filtered = $dss->filter(function ($item) use ($b, $date){
				  return $item->branchid == $b->id && $item->date->format('Y-m-d') == $date->format('Y-m-d') ? $item : null;
				})->first();

				$ds = $filtered;
				
				$data['date'] = $date->format('Y-m-d');
				$data['timestamp'] = $date->timestamp;

				if(is_null($ds)) {
					$data['sales'] 			= 0;
					$data['mancost'] 		= 0;
					$data['mancostpct']	= 0;
					$data['tips']				= 0;
					$data['tipspct']		= 0;
					$data['salesemp']		= 0;
					$data['purchcost']	= 0;
				} else {
					$data['sales'] 		= (float) $ds->sales;
					$data['mancost'] 	= (float) number_format($ds->empcount*$b->mancost,2,'.','');
					$data['mancostpct']	= (float) $ds->mancostpct;
					$data['tips']	=	(float) $ds->tips;
					$data['tipspct']	= (float) $ds->tipspct;
					$data['salesemp'] = $ds->empcount=='0' ? (float) '0.00':(float) number_format(($ds->sales/$ds->empcount),2,'.','');
					$data['purchcost']	= (float) $ds->purchcost;
				}

				array_push($arr[$b->code], $data);
			}
		}
		//return response()->json($arr);
		return response($arr)
							->header('Content-Type', 'text/json')
							->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 120))
							->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 120));
	}





	public function show2(Request $request, $branchid) {
		
		if (!is_uuid($branchid) && strlen($branchid)>3)
		 return abort('404');

		$branch = $this->repository->skipCriteria()->codeID($branchid);
		//$branch = $branches->first();

		if (is_null($branch))
			return abort('404');

		$branch->load('boss.user');
		return view('masterfiles.branch.view')->with('branch', $branch);


		return [
			'active'=>count($active),
			'inactive'=>count($inactive)
		];
	}








	/************* helpers **********/


	private function setViewWithDR($view){
		$response = new Response($view->with('dr', $this->dr));
		$response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 120));
		$response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 120));
		$response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 120));
		return $response;
	}


/******************* boss masterfiles stuff ***************************************************************/

	public function create(Request $request) {
		return view('masterfiles.branch.create');
	}

	public function store(Request $request) {
		
		if ($request->has('_type')) {
			switch ($request->input('_type')) {
				case 'quick':
					return $this->process_quick($request);
					break;
				case 'full':
					return $this->process_full($request);
					break;
				case 'import':
					return $this->process_import($request);
					break;
				case 'update':
					return $this->process_full($request);
					break;
			}
		} 
		return app()->environment('local') ? 'Honeypot not found!' : abort('404'); 
	}


	private function process_quick(Request $request) {
		//return $request->all();
		$brBoss = $this->repository->skipCriteria()->skipCache()->findWhere(['code'=>$request->input('code')])->first();
		if (!is_null($brBoss))
			return redirect()->back()->with('branch.import', $brBoss);
		
		$this->validate($request, [
    	'code' 				=> 'required|max:3',
      'descriptor' 	=> 'required|max:50',
      'email' 			=> 'max:50',
    ]);

    $cb = $this->branchBoss->findWhere(['code'=>$request->input('code')])->first();
    if (!is_null($cb))
			return redirect()->back()->withErrors(strtoupper($request->input('code')).' already exist on Boss Module');


		$c = strtolower($request->input('code'));
		$email = 'giligans.'.$c.'@gmail.com';
		$brcode = strtoupper($request->input('code'));
		$request->merge(['email'=>$email]);

		DB::beginTransaction();

		try {
    	$branch = $this->branchBoss->create(['code'=>strtoupper($request->code), 'descriptor'=>$request->descriptor]);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect('/masterfiles/branch/create')->withErrors($er);
		}
	
		try {
			$this->repository->modelCreate(['code'=>$branch->code, 'descriptor'=>$branch->descriptor, 'id'=>$branch->id]);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect('/masterfiles/branch/create')->withErrors($er);
		}

		if ($request->has('user') && $request->has('user')=='on') {
			$cashier = new User;
      $cashier->username = $c.'-cashier';
      $cashier->name = $brcode.' Cashier';
      $cashier->email = $email;
      $cashier->admin = 5;
      $cashier->branchid = $branch->id;
      $cashier->password = bcrypt('giligans');
      $cashier->id = \App\Models\Branch::get_uid();
      try {
				$cashier->save();
			} catch (Exception $e) {

			}

      $manager = new User;
      $manager->setConnection('mysql-tk');
      $manager->getTable(); // products
      $manager->setTable('users');
      $manager->username = $c.'-manager';
      $manager->name = $brcode.' Manager';
      $manager->email = $email;
      $manager->branchid = $branch->id;
      $manager->password = bcrypt('giligans');
      $manager->id = \App\Models\Branch::get_uid();
      try {
				$manager->save();
			} catch (Exception $e) {
				
			}
		}

		DB::commit();
    return redirect('/masterfiles/branch/'.$branch->lid())->with('alert-success','Saved!');
	}


	public function show(Request $request, $id) {
		$branch = $this->branchBoss->codeID($id);
		return is_null($branch) ? abort('404') : view('masterfiles.branch.view')->with('branch', $branch);
	}

	private function get_rules() {
		return $rules = [
    	'code' 					=> 'required|max:3',
      'descriptor' 		=> 'required|max:25',
      'trade_name' 		=> 'max:50',
      'address' 			=> 'max:120',
      'email' 				=> 'max:50|email',
      'tin' 					=> 'max:16',
      'company_id'		=> 'alpha_num|max:32',
      'sector_id'		  => 'alpha_num|max:32',
      'lessor_id'	  	=> 'alpha_num|max:32',
      'status'	    	=> 'integer',
      'seating'	    	=> 'numeric',
      'din'	    			=> 'integer',
      'kit'	    			=> 'integer',
      'mancost'	    	=> 'numeric',
      'ophr'	    		=> 'integer',
      'date_reg'  		=> 'date',
      'lessor_id'  		=> 'alpha_num|min:32:max:32',
    	'id' 						=> 'required|min:32:max:32',
    ];
	}


	private function unset_blank_form_rules(Request $request, array $rules, $id=true) {
		foreach ($rules as $key => $value) {
			if (empty($request->{$key}))
				unset($rules[$key]);
		}

		if ($id)
			unset($rules['id']);
		
		return $rules;
	}

	private function process_full(Request $request) {
		//return $request->all();
		if (!is_uuid($request->input('id')))
			return redirect('/masterfiles/branch')->withErrors('Something went wrong. Please try again');

		//$look_up_branch = $this->branchBoss->find($request->input('id'));

		//return $look_up_branch;

		$rules = $this->get_rules();

		if ($request->has('_type') && $request->input('_type')==='full') {
			$this->validate($request, $rules);
		} else if ($request->has('_type') && $request->input('_type')==='update') {
			unset($rules['code']);
			unset($rules['descriptor']);
			$this->validate($request, $rules);
		} else  {
			return redirect('/masterfiles/branch')->withErrors('Something went wrong. Please try again');
		}
		
		$keys = array_keys($this->unset_blank_form_rules($request, $rules));

		DB::beginTransaction();

		try {
    	$branch = $this->branchBoss->update($request->only($keys), $request->input('id'));
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		try {
    	$branch->contacts()->delete();
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		try {
    	$this->saveContacts($branch, $request->input('contact'));
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		try {
    	$branch->spaces()->delete();
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		try {
    	$this->saveSpaces($branch, $request->input('space'));
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		try {
    	$this->update_old_branch($request);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		DB::commit();
    return redirect('/masterfiles/branch/'.$branch->lid())->with('alert-success', 'Record has been updated!');
	}

	private function update_old_branch(Request $request) {

		$attr = [];

		if ($request->has('code'))
			$attr['code'] = $request->input('code');
		if ($request->has('descriptor'))
			$attr['descriptor'] = $request->input('descriptor');

		$u = $request->input('space');
    $ctr = count($u);
    if ($ctr>0) {
    	$a = '';
      foreach ($u as $key => $s) {
        if ($key==0)
          $a = $s['unit'];
        else if (($ctr-1)==$key)
          $a = $a.' and '.$s['unit'];
        else
          $a = $a.', '.$s['unit'];
      }
      $attr['address'] = $a.', '.$request->input('address');
    } else {
    	$attr['address'] = $request->input('address');
    }
    
    if ($request->input('contact')>0) {
    	foreach ($request->input('contact') as $key => $contact) {
    		if (!isset($attr['mobile']) && $contact['type']==1)
    			$attr['mobile'] = $contact['number'];
    		if (!isset($attr['phone']) && $contact['type']==2)
    			$attr['phone'] = $contact['number'];
    		if (!isset($attr['fax']) && $contact['type']==3)
    			$attr['fax'] = $contact['number'];
    	}
    }

    foreach (['email', 'tin', 'seating', 'mancost'] as $key => $value)
    	$attr[$value] = $request->input($value);

    $attr['sectorid'] = $request->input('sector_id');
    $attr['companyid'] = $request->input('company_id');
    $attr['opendate'] = $request->input('date_start');
    

    $this->repository->update($attr, $request->input('id'));
		
	}



	private function process_import(Request $request) {
		if (!is_uuid($request->input('id')))
			return abort('404');

		$hrBranch = $this->repository->find($request->input('id'));
		if (is_null($hrBranch))
			return redirect()->back()->withErrors('Record not found on HRIS Database.');
		
		$oc = [
			'code' => $hrBranch->code,
			'descriptor' => $hrBranch->descriptor,
			'address' => $hrBranch->address,
			'email' => $hrBranch->email,
			'tin' => $hrBranch->tin,
			'mancost' => $hrBranch->mancost,
			'date_start' => $hrBranch->opendate,
			'date_end' => $hrBranch->closedate,
			'seating' => $hrBranch->seating,
			'mancost' => $hrBranch->mancost,
			'company_id' => $hrBranch->companyid,
			'id' => $hrBranch->id,
		];

		DB::beginTransaction();

		try {
			$branch = $this->branchBoss->modelCreate($oc);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect('/masterfiles/branch')->withErrors($er);
		}

		$this->saveContacts($branch, [
			['type'=>1, 'number'=>$hrBranch->mobile],
			['type'=>2, 'number'=>$hrBranch->phone],
			['type'=>3, 'number'=>$hrBranch->fax],
		]);

		DB::commit();
		return redirect('/masterfiles/branch')->with('alert-success', $branch->code.' - '.$branch->descriptor.' has been imported to Boss Module.');
	}

	private function saveContacts($model, array $contacts) {
		foreach ($contacts as $key => $contact) {
			if (!empty($contact['number'])) {
				$contact['number'] = str_replace(['-', '(', ')', ' ', '.'], '', $contact['number']);
				$model->contacts()->save(new \App\Models\Contact($contact));
			}
		}
	}

	private function saveSpaces($model, array $spaces) {
		foreach ($spaces as $key => $space) {
			if (!empty($space['unit']) || $space['area']>0)
				$model->spaces()->save(new \App\Models\Boss\Space($space));
		}
	}

	public function edit(Request $request, $id) {
		$branch = $this->branchBoss->codeID($id);

		if (is_null($branch))
			return abort('404');

		$companies = $this->company->all(['code', 'descriptor', 'id']);
		$lessors = $this->lessor->all(['code', 'descriptor', 'id']);
		$sectors = $this->sector->with('children')->parents();

		return view('masterfiles.branch.edit')
							->with('branch', $branch)
							->with('companies', $companies)
							->with('sectors', $sectors)
							->with('lessors', $lessors);
	}






}