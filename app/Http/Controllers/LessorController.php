<?php namespace App\Http\Controllers;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\LessorRepository as LessorRepo;

class LessorController extends Controller
{

	protected $dr;
	protected $lessorRepo;

	public function __construct(DateRange $dr, LessorRepo $lessorRepo) {
		$this->dr = $dr;
		$this->lessorRepo = $lessorRepo;
	}

	public function create(Request $request) {
		return view('masterfiles.lessor.create');
	}

	public function show(Request $request, $id) {
		$lessor = $this->lessorRepo->with('branches')->codeID($id);
		return view('masterfiles.lessor.view')->with('lessor', $lessor);
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
		return 'Honeypot not found!'; 
	}

	private function process_quick(Request $request) {
		$this->validate($request, [
    	'code' => 'required|max:3',
      'descriptor' => 'required|max:50',
    ]);

		try {
    	$lessor = $this->lessorRepo->create(['code'=>strtoupper($request->code), 'descriptor'=>$request->descriptor]);
		} catch (Exception $e) {
			return redirect('/masterfiles/lessor/create')->withErrors($e->previous->errorInfo[2]);
		}

    return redirect('/masterfiles/lessor/'.$lessor->lid());
	}

	private function process_full(Request $request) {
		//return dd($request->input('contact'));
		if (!is_uuid($request->input('id')))
			return redirect('/masterfiles/lessor')->withErrors('Something went wrong. Please try again');

		$rules =  [
    	'code' 				=> 'required|max:3',
      'descriptor' 	=> 'required|max:25',
      'trade_name' 	=> 'max:50',
      'address' 		=> 'max:120',
      'email' 			=> 'max:50|email',
      'tin' 				=> 'max:16',
    	'id' 					=> 'required|min:32:max:32',
    ];

		if ($request->has('type') && $request->input('type')==='full') {
			$this->validate($request, $rules);
		} else if ($request->has('type') && $request->input('type')==='update') {
			unset($rules['code']);
			unset($rules['descriptor']);
			$this->validate($request, $rules);
		} else  {
			return redirect('/masterfiles/lessor')->withErrors('Something went wrong. Please try again');
		}
		unset($rules['id']);

		$keys = array_keys($rules);

		try {
    	$lessor = $this->lessorRepo->update($request->only($keys), $request->input('id'));
		} catch (Exception $e) {
			return redirect('/masterfiles/lessor/'.$request->lid())->withErrors($e->previous->errorInfo[2]);
		}

		$lessor->contacts()->delete();
		foreach ($request->input('contact') as $key => $v) {
			if (!empty($v['number']))
				$lessor->contacts()->save(new \App\Models\Contact($v));
		}

    return redirect('/masterfiles/lessor/'.$lessor->lid())->with('alert-success', 'Record has been updated!');
	}

	public function edit(Request $request, $id) {
		$lessor = $this->lessorRepo->codeID($id);
		return view('masterfiles.lessor.edit')->with('lessor', $lessor);
	}


	

  


}