<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Validator;
use Auth;
use App\Events\UserChangePassword;
use App\Models\Branch;
use App\Models\BossBranch;
use App\Repositories\BossBranchRepository;
use App\Repositories\Criterias\BossBranchCriteria;

class SettingsController extends Controller {

	protected $repository;

	public function __construct(BossBranchRepository $repository) {
		$this->repository = $repository;
		$this->repository->pushCriteria(new BossBranchCriteria);
	}



	public function getIndex(Request $request, $param1=null, $param2=null){
		if(strtolower($param1)==='add')
			return $this->makeAddView($request);
		else if(preg_match('/(20[0-9][0-9])/', $param1) && (strtolower($param2)==='week') && preg_match('/^[0-9]+$/', $param3)) //((strtolower($param1)==='week') && preg_match('/^[0-9]+$/', $param2)) 
			return $this->makeViewWeek($request, $param1, $param3); //task/mansked/2016/week/7
		else if(preg_match('/^[A-Fa-f0-9]{32}+$/', $param1) && strtolower($param2)==='edit')
			return $this->makeEditView($request, $param1);
		else if($param1==='bossbranch' && $param2==null)   //preg_match('/^[A-Fa-f0-9]{32}+$/',$action))
			return $this->makeBossbranchView($request, $param1, $param2);
		else if($param1==='password' && $param2==null)   //preg_match('/^[A-Fa-f0-9]{32}+$/',$action))
			return $this->makePasswordView($request, $param1, $param2);
		else
			return $this->makeIndexView($request, $param1, $param2);
	}



	public function makeIndexView(Request $request, $p1, $p2) {

		$user = User::where('id', $request->user()->id)
					->first();
	
		return view('settings.index')->with('user', $user);	
	}

	public function makePasswordView(Request $request, $p1, $p2) {

	
		return view('settings.password');	
	}

	public function changePassword(Request $request) {

		$rules = array(
			'passwordo'      => 'required|max:50',
			'password'      	=> 'required|confirmed|max:50|min:8',
			'password_confirmation' => 'required|max:50|min:8',
		);

		$messages = [
	    'passwordo.required' => 'Old password is required.',
	    'password.required' => 'New password is required.',
		];
		
		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails())
			return redirect('/settings/password')->withErrors($validator);

		if (!Auth::attempt(['username'=>$request->user()->username, 'password'=>$request->input('passwordo')]))
			return redirect('/settings/password')->withErrors(['message'=>'Invalid old password.']);

		$user = User::find($request->user()->id)
								->update(['password' => bcrypt($request->input('password'))]);
		
		if(!$user)
			return redirect('/settings/password')->withErrors(['message'=>'Unable to change password.']);
		
		event(new UserChangePassword($request));

		return redirect('/settings/password')->with('alert-success', 'Password change!');
		return view('settings.password');	
	}



	public function makeBossbranchView(Request $request, $p1, $p2) {

		$arr = [];

		$bb = $this->repository->all(['branchid']);
		//return $bb;
		
		$branchs = Branch::select(['code', 'descriptor', 'id'])->orderBy('code', 'ASC')->get();
		$i = 0;
		foreach ($branchs as $branch) {
			$b = $bb->where('branchid', $branch->id)->first();
			$arr[$i]['branch'] = $branch;
			$arr[$i]['assign'] = is_null($b) ? null : $branch->id;
			$i++;
		}
		
		
		
		

		//return $arr;
		
		return view('settings.bossbranch')->with('datas', $arr);	
	}


	public function assignBranch(Request $request){


		if($request->ajax()){
			$b = BossBranch::where('branchid', $request->input('branchid'))
								->where('bossid', $request->user()->id) 
								->first();
				// false = create & null
			if ($request->input('assign') && is_null($b)) 
				//$this->repository->create()
				BossBranch::create(['branchid' => $request->input('branchid'), 'bossid' => $request->user()->id]);
			//elseif (!$request->input('assign') && !is_null($b)) // true = delete & not null
			//	echo ''; //$b->delete();
			else 
				$b->delete();
		}

		return json_encode(['branch'=>$b, 'assign'=>$request->input('assign')]);
	}



	
}