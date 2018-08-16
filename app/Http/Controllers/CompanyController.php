<?php namespace App\Http\Controllers;

use DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\CompanyRepository as CompanyRepo;
use App\Repositories\Boss\CompanyRepository as CompanyBoss;

class CompanyController extends Controller
{

	protected $dr;
	protected $companyRepo;
	protected $companyBoss;

	public function __construct(DateRange $dr, CompanyRepo $companyRepo, CompanyBoss $companyBoss) {
		$this->dr = $dr;
		$this->companyRepo = $companyRepo;
		$this->companyBoss = $companyBoss;
	}


	public function index(Request $request) {
		
	}

	public function create(Request $request) {
		return view('masterfiles.company.create');
	}

	public function show(Request $request, $id) {
		$company = $this->companyBoss->codeID($id);
		return is_null($company) ? abort('404') : view('masterfiles.company.view')->with('company', $company);
	}

	public function store(Request $request) {
		
		if ($request->has('type')) {
			switch ($request->input('type')) {
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
		$this->validate($request, [
    	'code' => 'required|max:3',
      'descriptor' => 'required|max:50',
    ]);

    $cc = $this->companyBoss->findWhere(['code'=>$request->input('code')])->first();
    if (!is_null($cc))
			return redirect()->back()->withErrors(strtoupper($request->input('code')).' already exist on Boss Module');

		$hrComp = $this->companyRepo->findWhere(['code'=>$request->input('code')])->first();
		if (!is_null($hrComp))
			return redirect()->back()->with('company.import', $hrComp);

		DB::beginTransaction();

		try {
    	$company = $this->companyBoss->create(['code'=>strtoupper($request->code), 'descriptor'=>$request->descriptor]);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect('/masterfiles/company/create')->withErrors($er);
		}

		try {
			$this->companyRepo->modelCreate(['code'=>$company->code, 'descriptor'=>$company->descriptor, 'id'=>$company->id]);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect('/masterfiles/company/create')->withErrors($er);
		}

		DB::commit();
    return redirect('/masterfiles/company/'.$company->lid());
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
		
		if (!is_uuid($request->input('id')))
			return redirect('/masterfiles/company')->withErrors('Something went wrong. Please try again');

		$rules =  [
    	'code' 					=> 'required|max:3',
      'descriptor' 		=> 'required|max:50',
      'address' 			=> 'max:120',
      'email' 				=> 'max:50|email',
      'tin' 					=> 'max:16',
      'sss_no' 				=> 'max:20',
      'philhealth_no'	=> 'max:20',
      'hdmf_no' 			=> 'max:20',
    	'id' 						=> 'required|min:32:max:32',
    ];

		if ($request->has('type') && $request->input('type')==='full') {
			$this->validate($request, $rules);
		} else if ($request->has('type') && $request->input('type')==='update') {
			unset($rules['code']);
			unset($rules['descriptor']);
			$this->validate($request, $rules);
		} else  {
			return redirect('/masterfiles/company')->withErrors('Something went wrong. Please try again');
		}

		//unset($rules['id']);
		//$keys = array_keys($rules);
		$keys = array_keys($this->unset_blank_form_rules($request, $rules));

		DB::beginTransaction();

		try {
    	$company = $this->companyBoss->update($request->only($keys), $request->input('id'));
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		try {
    	$company->contacts()->delete();
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		foreach ($request->input('contact') as $key => $v) {
			if (!empty($v['number']))
				$company->contacts()->save(new \App\Models\Contact($v));
		}

		try {
    	$this->update_old_company($request);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		DB::commit();
    return redirect('/masterfiles/company/'.$company->lid())->with('alert-success', 'Record has been updated!');
	}

	private function update_old_company(Request $request) {

		$attr = [];

		 foreach (['code', 'descriptor', 'address', 'tin'] as $key => $value) {
		 		if ($request->has($value) && !empty($request->input($value)))
    			$attr[$value] = $request->input($value);
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

    $this->companyRepo->update($attr, $request->input('id'));
	}

	private function process_import(Request $request) {
		
		if (!is_uuid($request->input('id')))
			return abort('404');

		$hrComp = $this->companyRepo->find($request->input('id'));
		if (is_null($hrComp))
			return redirect()->back()->withErrors('Record not found on HRIS Database.');
		
		$oc = [
			'code' => $hrComp->code,
			'descriptor' => $hrComp->descriptor,
			'address' => $hrComp->address,
			'email' => $hrComp->email,
			'tin' => $hrComp->tin,
			'id' => $hrComp->id,
		];

		DB::beginTransaction();

		try {
			$company = $this->companyBoss->modelCreate($oc);
		} catch (Exception $e) {
			DB::rollBack();
			return redirect('/masterfiles/company')->withErrors($e->previous->errorInfo[2]);
		}

		$this->saveContacts($company, [
			['type'=>1, 'number'=>$hrComp->mobile],
			['type'=>2, 'number'=>$hrComp->phone],
			['type'=>3, 'number'=>$hrComp->fax],
		]);

		DB::commit();
		return redirect('/masterfiles/company')->with('alert-success', $company->code.' - '.$company->descriptor.' has been imported to Boss Module.');
	}

	private function saveContacts($model, array $contacts) {
		foreach ($contacts as $key => $contact) {
			if (!empty($contact['number']))
				$model->contacts()->save(new \App\Models\Contact($contact));
		}
	}

	public function edit(Request $request, $id) {
		$company = $this->companyRepo->codeID($id);
		return view('masterfiles.company.edit')->with('company', $company);
	}


	

  


}