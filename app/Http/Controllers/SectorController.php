<?php namespace App\Http\Controllers;

use DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Boss\SectorRepository as SectorRepo;


class SectorController extends Controller
{

	protected $sector;

	public function __construct(SectorRepo $sectorRepo) {
		$this->sector = $sectorRepo;
	}

	
	public function create(Request $request) {
		$parents = $this->sector->parents();
		return view('masterfiles.sector.create')->with('parents', $parents);
	}

	public function show(Request $request, $id) {
		$sector = $this->sector->with('children')->codeID($id);
		return is_null($sector) ? abort('404') : view('masterfiles.sector.view')->with('sector', $sector);
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
		//return $request->all();

		$rules = [
    	'code' => 'required|max:3',
      'descriptor' => 'required|max:50',
      'parent_id' 		=> 'max:32',
    ];

		$this->validate($request, $rules);

		DB::beginTransaction();

		$keys = array_keys($rules);

		try {
    	$sector = $this->sector->create($request->only($keys));
		} catch (Exception $e) {
			DB::rollBack();
			return redirect('/masterfiles/sector/create')->withErrors($e->previous->errorInfo[2]);
		}

		DB::commit();
    return redirect('/masterfiles/sector/'.$sector->lid());
	}

	private function process_full(Request $request) {
		//return dd($request->all());
		if (!is_uuid($request->input('id')))
			return redirect('/masterfiles/sector')->withErrors('Something went wrong. Please try again');

		$u_sector = $this->sector->find($request->input('id'));
		if (is_null($u_sector))
			return redirect('/masterfiles/sector')->withErrors('Something went wrong. Please try again');

		$rules =  [
    	'code' 					=> 'required|max:3',
      'descriptor' 		=> 'required|max:25',
      'parent_id' 		=> 'max:32',
    	'id' 						=> 'required|min:32|max:32',
    ];

		if ($request->has('type') && $request->input('type')==='update') {
			$this->validate($request, $rules);
		} else  {
			return redirect('/masterfiles/sector')->withErrors('Something went wrong. Please try again');
		}
		unset($rules['id']);

		$keys = array_keys($rules);

		DB::beginTransaction();

		try {
    	$sector = $this->sector->update($request->only($keys), $request->input('id'));
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		DB::commit();
    return redirect('/masterfiles/sector/'.$sector->lid())->with('alert-success', 'Record has been updated!');
	}

	public function edit(Request $request, $id) {
		$parents = $this->sector->parents();
		$sector = $this->sector->codeID($id);
		return view('masterfiles.sector.edit')->with('sector', $sector)->with('parents', $parents);
	}


	

  


}