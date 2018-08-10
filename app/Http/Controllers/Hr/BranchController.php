<?php namespace App\Http\Controllers\Hr;

use Exception;
use Carbon\Carbon;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\BranchRepository;
use App\Repositories\Boss\BranchRepository as BossBr;
use App\Repositories\Criterias\ActiveBranchCriteria as ActiveBranch;


class BranchController extends Controller 
{

	protected $repository;
	protected $branchBoss;
	protected $branches;

	public function __construct(BranchRepository $branchrepository, BossBr $branchBoss){

		$this->repository = $branchrepository;
		$this->branchBoss = $branchBoss;

		$this->repository->pushCriteria(new ActiveBranch);
		$this->branches = $this->repository->all();
	}

	public function getBranch(Request $request) {
		return view('hr.masterfiles.employee.branch-emp')->with('branch', false)->with('branches', $this->branches);
	}

	public function branchEmployee(Request $request, $branchid) {
		
		try {
			$o = $this->repository->skipCriteria()->find($branchid);
		} catch (Exception $e) {
			return redirect('/hr/masterfiles/employee/branch')->withErrors('Unknown Branch.');
		}

		return view('hr.masterfiles.employee.branch-emp')->with('branch', $o)->with('branches', $this->branches);

	}







}