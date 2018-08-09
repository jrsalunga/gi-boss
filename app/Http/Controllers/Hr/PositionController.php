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
			return redirect()->back()->withErrors('Something went wrong. Please try again');

		$rules =  [
    	'code' 					=> 'required|max:3',
      'descriptor' 		=> 'required|max:25',
    	'id' 						=> 'required|min:32:max:32',
    ];

		$this->validate($request, $rules);

		unset($rules['id']);

		$keys = array_keys($rules);

		DB::beginTransaction();
		try {
    	$model = $this->repository->update($request->only($keys), $request->input('id'));
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}
		DB::commit();

    return redirect('/hr/masterfiles/'.$this->getTable().'/'.$model->lid())->with('alert-success', 'Record has been updated!');
	}

	

	public function edit(Request $request, $id) {
		$model = $this->repository->codeID($id);
		return view('hr.masterfiles.'.$this->getTable().'.edit')->with('model', $model)->with('table', $this->getTable());
	}
















	public function getTable() {
		return $this->table;
	}


	

  


}