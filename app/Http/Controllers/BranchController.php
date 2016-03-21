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
use App\Repositories\DailySalesRepository as DSRepo;
use App\Repositories\Criterias\ActiveBranchCriteria as ActiveBranch;



class BranchController extends Controller 
{

	protected $repository;

	public function __construct(BranchRepository $branchrepository, DSRepo $dsrepo, DateRange $dr){

		$this->repository = $branchrepository;
		$this->repository->pushCriteria(new ActiveBranch);
		$this->dr = $dr;
		$this->ds = $dsrepo;
	}

	//status/branch/{branchid}
	public function getStatus(Request $request, $branchid = NULL) {

		$branches = $this->repository->all(['code', 'descriptor', 'id']);
		
		if (is_null($branchid)) {
			$branch = null;
			$dailysales = null;
			return $this->setViewWithDR(view('status.branch')
									->with('dailysales', $dailysales)
									->with('branches', $branches)
									->with('branch', $branch));
		}
		
		if (!is_uuid($branchid)) 
			return redirect('/status/branch')->withErrors(['message'=>'Invalid branchid']);

		$branch = $this->repository->find($branchid, ['code', 'descriptor', 'mancost', 'id']);

		$dailysales = $this->ds->branchByDR($branch, $this->dr);
		
		return $this->setViewWithDR(view('status.branch')
								->with('dailysales', $dailysales)
								->with('branches', $branches)
								->with('branch', $branch));
	}

	public function postStatus(Request $request) {

		$fr = carbonCheckorNow($request->input('fr'));
		$to = carbonCheckorNow($request->input('to'));

		if ($to->lt($fr)) {
			$to = Carbon::now();
			$fr = $to->copy()->subDay(30); //$fr = $to->copy()->subDay(7);
		} 

		$this->dr->fr = $fr;
		$this->dr->to = $to;
		$this->dr->date = $to;

		$rules = array(
			'branchid' 	=> 'required|max:32|min:32',
		);

		$messages = [
	    'branchid.required' 	=> 'Please a select Branch.',
	    'branchid.min' 				=> 'Please a select Branch..',
	    'branchid.max' 				=> 'Please a select Branch...',
		];
		
		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails())
			return redirect('/status/branch')->withErrors($validator)
							->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 120))
							->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 120))
							->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 120));

		return redirect('/status/branch/'.strtolower($request->input('branchid')))
							->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 120))
							->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 120))
							->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 120));
		
		return $request->all();
	}

	public function getComparative(Request $request) {

		$branches = $this->repository->all(['code', 'descriptor', 'mancost', 'id']);

		return $this->setViewWithDR(view('status.comparative')
								->with('branches', $branches)); 
	}

	public function postComparative(Request $request) {

		return  $request->input('branch');
		$branches = $this->repository->all(['code', 'descriptor', 'mancost', 'id']);

		return $this->setViewWithDR(view('status.comparative')
								->with('branches', $branches)); 
	}

	public function getComparativeCSV(Request $request){

		$this->dr->fr = carbonCheckorNow($request->input('fr'));
		$this->dr->to = carbonCheckorNow($request->input('to'));


		$branches = $this->repository->findWhereIn('id', $request->input('branches'), ['code', 'descriptor', 'mancost', 'id']);

		$dss = $this->ds->with(['branch'=>function($query){
						$query->select(['code', 'descriptor', 'mancost', 'id']);
					}])
					->scopeQuery(function($query) use ($request) {
             return $query->whereBetween('date', [$this->dr->fr->format('Y-m-d'), $this->dr->to->format('Y-m-d')])
              						->whereIn('branchid', $request->input('branches'))
                          ->orderBy('date', 'ASC');
          })->all();


		echo "Date";
		foreach ($branches as $b) {
			echo ','.$b->code;
		}

		echo PHP_EOL;

		
		foreach ($this->dr->dateInterval() as $date) {
			echo $date->format('Y-m-d');

			foreach ($branches as $b) {
				
				$filtered = $dss->filter(function ($item) use ($b, $date){
				  return $item->branchid == $b->id && $item->date->format('Y-m-d') == $date->format('Y-m-d') ? $item : null;
				})->first();

				$ds = $filtered;

				echo ',';

				if(is_null($ds))
					echo 0;
				else {
					
					if($request->input('stat')==2)
						echo number_format($ds['empcount']*$b->mancost,2,'.','');
					elseif($request->input('stat')==3)
						echo $ds['tips'];
					elseif($request->input('stat')==4) 
						echo $ds->empcount=='0' ? '0.00':number_format(($ds->sales/$ds->empcount),2,'.','');
					else
						echo $ds['sales'];
				}

			}
				echo PHP_EOL;
		}

		return;
	}














	/************* helpers **********/


	private function setViewWithDR($view){
		$response = new Response($view->with('dr', $this->dr));
		$response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 120));
		$response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 120));
		$response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 120));
		return $response;
	}


}