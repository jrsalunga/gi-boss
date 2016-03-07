<?php namespace App\Http\Controllers;

use File;
use Exception;
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

		return $this->setViewWithDR(view('status.branch'));
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