<?php namespace App\Http\Controllers;

use File;
use Exception;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Branch;
use App\Repositories\BranchRepository;
use App\Repositories\DateRange;




class BranchController extends Controller 
{

	protected $repository;

	public function __construct(BranchRepository $branchrepository, DateRange $dr){

		$this->repository = $branchrepository;
		$this->dr = $dr;
	}




	public function getStatus(Request $request, $branchid = NULL) {

		$branches = $this->repository->all(['code', 'descriptor', 'id']);
		return $this->setViewWithDR(view('status.branch')->with('branches', $branches));
	}

	public function postStatus(Request $request) {

		//return is_uuid($request->input('branchid'));
		//return $request->all();

		$rules = array(
			'fr'				=> 'required|max:10|min:10',
			'to' 				=> 'required|max:10|min:10',
			'branchid' 	=> 'required|max:32|min:32',
		);

		$messages = [
	    'fr.required' 				=> 'From date is required.',
	    'to.required' 				=> 'To date is required.',
	    'branchid.required' 	=> 'Please a select Branch.',
	    'fr.max' 							=> 'From date is required...',
	    'to.max' 							=> 'To date is required...',
	    'branchid.max' 				=> 'Please a select Branch...',
	    'fr.min' 							=> 'From date is required..',
	    'to.min' 							=> 'To date is required..',
	    'branchid.min' 				=> 'Please a select Branch..',
		];
		
		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails())
			return redirect('/status/branch')->withErrors($validator);


		if (!is_uuid($request->input('branchid'))) 
			return redirect('/status/branch')->withErrors(['message'=>'Invalid branchid']);

		$branch = $this->repository->find($request->input('branchid') , ['code', 'descriptor', 'id']);

		return redirect('/status/branch/'.$branch->lid())
							->withCookie(cookie('test-cookie', 'test', 1));
		return $request->all();

	}














	/************* helpers **********/


	private function setViewWithDR($view){
		$response = new Response($view->with('dr', $this->dr));
		$response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
		$response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
		$response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
		return $response;
	}


}