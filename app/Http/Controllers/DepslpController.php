<?php namespace App\Http\Controllers;
use URL;
use Event;
use StdClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\BranchRepository;
use App\Repositories\StorageRepository;
use Dflydev\ApacheMimeTypes\PhpRepository;
use App\Repositories\DepslipRepository as DepslpRepo;
use App\Repositories\Criterias\ActiveBranchCriteria as ActiveBranch;
use App\Events\Depslp\Change as DepslpChange;
use App\Events\Depslp\Delete as DepslpDelete;

class DepslpController extends Controller { 

	protected $depslip;
	protected $branch;

	public function __construct(DepslpRepo $depslip, BranchRepository $branch) {
		$this->depslip = $depslip;
		$this->branch = $branch;

		$this->files = new StorageRepository(new PhpRepository, 'files.'.app()->environment());
	}

	public function getHistory(Request $request) {
		
		$depslips = $this->depslip
			->skipCache()
			->with(['fileUpload'=>function($query){
        $query->select(['filename', 'terminal', 'id']);
      },'branch'=>function($query){
        $query->select(['code', 'descriptor', 'id']);
      }])
      ->orderBy('created_at', 'DESC')
      ->paginate(10);
				
		return view('docu.depslp.index')->with('depslips', $depslips);
	}

	public function getChecklist(Request $request) {

		$bb = $this->branch
  						->orderBy('code')
  						->getByCriteria(new ActiveBranch)
  						->all(['code', 'descriptor', 'id']);
		$date = carbonCheckorNow($request->input('date'));

		if(!$request->has('branchid') && !isset($_GET['branchid'])) {
      return view('docu.depslp.checklist')
						->with('date', $date)
						->with('depslips', null)
						->with('branches', $bb)
						->with('branch', null);
    } 


    if(!is_uuid($request->input('branchid'))
    || !in_array(strtoupper($request->input('branchid')),  $this->branch->all()->pluck('id')->all())) 
    {
      return redirect('/depslp/checklist')->with('alert-warning', 'Please select a branch.');
    } 

    try {
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    } catch (Exception $e) {
      return redirect('/backup/checklist')->with('alert-warning', 'Please select a branch.');
    }

  	$depslips = $this->depslip->skipCache()->monthlyLogs($date, strtoupper($branch->code)	);

  	if($request->has('debug'))
  		return $depslips;
  	
  	return view('docu.depslp.checklist')
  					->with('date', $date)
  					->with('branches', $bb)
  					->with('branch', $branch)
  					->with('depslips', $depslips);

		
	}

	public function getAction($brcode, $id=null, $action=null) {
		if(!is_uuid($id) || $brcode!==strtolower(session('user.branchcode')))
			return redirect($brcode.'/depslp/log');

		if (strtolower($action)==='edit')
			return $this->editDepslp($id);
		else
			return $this->viewDepslp($id);
	}


	private function verify($id, $userid, $matched=0) {
		return $this->depslip->update([
			'verified' 	=> 1,
			'matched'		=> $matched,
			'user_id'		=> $userid,
			'updated_at' 	=> c()
		], $id);
	}

	private function checkVerify($id) {

		if(request()->has('user_id') && is_uuid(request()->input('user_id')))
			$userid = strtoupper(request()->input('user_id'));
		else
			$userid = strtoupper(request()->user()->id);

		if(request()->has('verified') && request()->input('verified')==true)
			return $this->verify($id, '41F0FB56DFA811E69815D19988DDBE1E');
		else if(request()->has('verify') && request()->input('verify')==true)
			return $this->verify($id, $userid);
		else
			return false;
	}



	private function viewDepslp($id) {
		$depslp = $this->depslip->find($id);
		if(!$depslp->verified)
			if($this->checkVerify($depslp->id))
				return $this->viewDepslp($id);
		return view('docu.depslp.view', compact('depslp'));
	}

	private function editDepslp($id) {
		$depslp = $this->depslip->find($id);
		if($depslp->verified || $depslp->matched)
			return $this->viewDepslp($id);
		return view('docu.depslp.edit', compact('depslp'));
	}

	public function getImage(Request $request, $brcode, $filename) {

		$id = explode('.', $filename);

		if(!is_uuid($id[0]) || $brcode!==strtolower(session('user.branchcode')))
			return abort(404);

		$d = $this->depslip->find($id[0]);

		$path = $this->getPath($d);

		if(!$this->files->exists($this->getPath($d)))
			return abort(404);

		if($request->has('download') && $request->input('download')==='true') {
    	return response($this->files->get($path), 200)
	 						->header('Content-Type', $this->files->fileMimeType($path))
  						->header('Content-Disposition', 'attachment; filename="'.$d->filename.'"');
		}

		return response($this->files->get($path), 200)
	 						->header('Content-Type', $this->files->fileMimeType($path));

	}

	public function put(Request $request) {

		$rules = [
			'date'				=> 'required|date',
			'time'				=> 'required',
			'amount'			=> 'required',
			'cashier'			=> 'required',
			'id'					=> 'required',
		];

		$validator = app('validator')->make($request->all(), $rules);

		if ($validator->fails()) 
			return redirect()->back()->withErrors($validator);
		
		$old_depslip = $this->depslip->find($request->input('id'));
		if(!is_null($old_depslip)) {
			
			$d = $this->depslip->update([
				'date' 				=> request()->input('date'),
	    	'time' 				=> request()->input('time'),
	    	'amount' 			=> str_replace(",", "", request()->input('amount')),
	    	'cashier' 		=> $request->input('cashier'),
	    	'remarks' 		=> $request->input('notes'),
	    	'updated_at' 	=> c()
			], $request->input('id'));

			//Event::fire('depslp.changed', ['new'=>$old_depslip]);
			$arr = array_diff($old_depslip->toArray(), $d->toArray());
			array_forget($arr, 'updated_at');
			
			if (app()->environment()==='production')
				event(new DepslpChange($old_depslip, $d, $arr));

			return redirect(brcode().'/depslp/'.$d->lid())
							->with('alert-success', 'Deposit slip is updated!');
		}

		return redirect()->back()->withErrors('Deposit Slip not found!');
	}



	private function getPath($d) {
		return 'DEPSLP'.DS.$d->date->format('Y').DS.session('user.branchcode').DS.$d->date->format('m').DS.$d->filename;
	}

	public function delete(Request $request) {

		$validator = app('validator')->make($request->all(), ['id'=>'required'], []);

		if ($validator->fails()) 
			return redirect()->back()->withErrors($validator);

		$depslp = $this->depslip->find($request->input('id'));

		if (is_null($depslp))
			return redirect()->back()->withErrors('Deposit slip not found!');

		if (!$depslp->isDeletable())
			return redirect()->back()->withErrors($depslp->fileUpload->filename.' deposit slip is not deletable, already verified!');

		if ($this->depslip->delete($depslp->id)) {
			
			if ($this->files->exists($this->getPath($depslp)))
				$this->files->deleteFile($this->getPath($depslp));

			if (app()->environment()==='production')
				event(new DepslpDelete($depslp->toArray()));

			return redirect(brcode().'/depslp/log')
							->with('depslp.delete', $depslp)
							->with('alert-important', true);
		}
		return redirect()->back()->withErrors('Error while deleting record!');
	}






	






}