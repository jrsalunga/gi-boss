<?php namespace App\Http\Controllers\Hr;

use DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\PositionRepository as PositionRepo;

class PositionController extends Controller
{

	protected $dr;
	protected $position;
	protected $table = 'position';

	public function __construct(DateRange $dr, PositionRepo $position) {
		$this->dr = $dr;
		$this->repository = $position;
	}

	public function create(Request $request) {
		return view('hr.masterfiles.'.$this->getTable().'.create')->with('table', $this->getTable());
	}

	public function show(Request $request, $id) {
		$model = $this->repository->codeID($id);
		return is_null($model) 
			? abort('404') 
			: view('hr.masterfiles.'.$this->getTable().'.view')
						->with('model', $model)
						->with('table', $this->getTable());
	}

	public function store(Request $request) {
		
		if ($request->has('type')) {
			switch ($request->input('type')) {
				case 'quick':
					return $this->process_quick($request);
					break;
				case 'full':
					return $this->process_quick($request);
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
    	'code' 				=> 'required|anshu|max:3',
      'descriptor' 	=> 'required|anshu|max:50',
    ]);

		DB::beginTransaction();

		try {
    	$model = $this->repository->create(['code'=>strtoupper($request->code), 'descriptor'=>$request->descriptor]);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		DB::commit();
    return redirect('/hr/masterfiles/'.$this->getTable().'/'.$model->lid());
	}

	private function process_full(Request $request) {
		//return dd($request->all());
		if (!is_uuid($request->input('id')))
			return redirect('/masterfiles/company')->withErrors('Something went wrong. Please try again');

		$rules =  [
    	'code' 					=> 'required|max:3',
      'descriptor' 		=> 'required|max:25',
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
		unset($rules['id']);

		$keys = array_keys($rules);

		DB::beginTransaction();

		try {
    	$company = $this->repository->update($request->only($keys), $request->input('id'));
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
			//return redirect('/masterfiles/company/'.$request->input('id'))->withErrors($e->previous->errorInfo[2]);
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

		DB::commit();
    return redirect('/masterfiles/company/'.$company->lid())->with('alert-success', 'Record has been updated!');
	}

	private function process_import(Request $request) {
		if (!is_uuid($request->input('id')))
			return abort('404');

		$hrComp = $this->companyHris->find($request->input('id'));
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
			$company = $this->repository->modelCreate($oc);
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
		$model = $this->repository->codeID($id);
		return view('hr.masterfiles.'.$this->getTable().'.edit')->with('model', $model)->with('table', $this->getTable());
	}
















	public function getTable() {
		return $this->table;
	}


	

  


}