<?php namespace App\Http\Controllers;

use DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\FiletypeRepository as FileTypeRepo;


class FiletypeController extends Controller
{

	protected $fileType;

	public function __construct(FileTypeRepo $fileTypeRepo) {
		$this->fileType = $fileTypeRepo;
	}

	
	public function create(Request $request) {
		return view('masterfiles.filetype.create');
	}

	public function show(Request $request, $id) {
		$filetype = $this->fileType->codeID($id);
		return is_null($filetype) ? abort('404') : view('masterfiles.filetype.view')->with('filetype', $filetype);
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
    	'code' 				=> 'required|max:10',
      'descriptor' 	=> 'required|max:120',
      'assigned' 		=> 'max:3',
    ];

		$this->validate($request, $rules);

		DB::beginTransaction();

		$keys = array_keys($rules);

		try {
    	$filetype = $this->fileType->create($request->only($keys));
		} catch (Exception $e) {
			DB::rollBack();
			return redirect('/masterfiles/filetype/create')->withErrors($e->previous->errorInfo[2]);
		}

		DB::commit();
    return redirect('/masterfiles/filetype/'.$filetype->lid());
	}

	private function process_full(Request $request) {
		//return dd($request->all());
		if (!is_uuid($request->input('id')))
			return redirect('/masterfiles/filetype')->withErrors('Something went wrong. Please try again');

		$verified = $this->fileType->find($request->input('id'));
		if (is_null($verified))
			return redirect('/masterfiles/filetype')->withErrors('Something went wrong. Please try again');

		$rules =  [
    	'code' 					=> 'required|max:10',
      'descriptor' 		=> 'required|max:120',
      'assigned' 			=> 'max:3',
    	'id' 						=> 'required|min:32|max:32',
    ];

		if ($request->has('type') && $request->input('type')==='update') {
			$this->validate($request, $rules);
		} else {
			return redirect('/masterfiles/filetype')->withErrors('Something went wrong. Please try again');
		}
		unset($rules['id']);

		$keys = array_keys($rules);

		DB::beginTransaction();

		try {
    	$filetype = $this->fileType->update($request->only($keys), $verified->id);
		} catch (Exception $e) {
			$er = isset($e->previous->errorInfo[2]) ? $e->previous->errorInfo[2] : $e->getMessage();
			DB::rollBack();
			return redirect()->back()->withErrors($er);
		}

		DB::commit();
    return redirect('/masterfiles/filetype/'.$filetype->lid())->with('alert-success', 'Record has been updated!');
	}

	public function edit(Request $request, $id) {
		$filetype = $this->fileType->codeID($id);
		return view('masterfiles.filetype.edit')->with('filetype', $filetype);
	}


	

  


}