<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Criterias\DateCriteria;
use App\Repositories\DailySalesRepository as DSRepo;
use App\Repositories\BossBranchRepository as BBRepo;
use App\Repositories\Criterias\BossBranchCriteria;
use App\Repositories\Criterias\BranchDailySalesCriteria;
use Exception;

class DashboardController extends Controller 
{

	protected $repo;


	public function __construct(DSRepo $dsrepo, BBRepo $bbrepo) {
		$this->repo = $dsrepo;
	}


	public function getIndex(Request $request) {

		$date = carbonCheckOrNow($request->input('date'));

		$dailysales = $this->repo->branchByDate($date);
		
		return view('dashboard')->with('dailysales', $dailysales)->with('date', $date);
	}
}