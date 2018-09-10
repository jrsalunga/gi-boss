<?php namespace App\Http\Controllers;
use URL;
use Event;
use StdClass;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\BranchRepository;
use App\Repositories\StorageRepository;
use Dflydev\ApacheMimeTypes\PhpRepository;
use App\Repositories\SetslpRepository as SetslpRepo;
use App\Repositories\Criterias\ActiveBranchCriteria as ActiveBranch;
use App\Repositories\DailySalesRepository as DSRepo;

class SetslpController extends Controller { 

	protected $setslp;
	protected $branch;
	protected $ds;

	public function __construct(SetslpRepo $setslp, BranchRepository $branch, DSRepo $dsrepo) {
		$this->setslp = $setslp;
		$this->branch = $branch;
		$this->ds = $dsrepo;

		$this->files = new StorageRepository(new PhpRepository, 'files.'.app()->environment());
	}

	public function getHistory(Request $request) {
		
		$setslps = $this->setslp
			->skipCache()
			->with(['fileUpload'=>function($query){
        $query->select(['filename', 'size', 'terminal', 'id']);
      },'branch'=>function($query){
        $query->select(['code', 'descriptor', 'id']);
      }])
      ->orderBy('created_at', 'DESC')
      ->paginate(10);
				
		return view('docu.setslp.index')->with('setslps', $setslps);
	}

	public function getChecklist(Request $request) {

		$bb = $this->branch
  						->orderBy('code')
  						->getByCriteria(new ActiveBranch)
  						->all(['code', 'descriptor', 'id']);
		$date = carbonCheckorNow($request->input('date'));

		if(!$request->has('branchid') && !isset($_GET['branchid'])) {
      return view('docu.setslp.checklist')
						->with('date', $date)
						->with('setslps', null)
						->with('branches', $bb)
						->with('branch', null);
    } 


    if(!is_uuid($request->input('branchid'))
    || !in_array(strtoupper($request->input('branchid')),  $this->branch->all()->pluck('id')->all())) 
    {
      return redirect('/setslp/checklist')->with('alert-warning', 'Please select a branch.');
    } 

    try {
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    } catch (Exception $e) {
      return redirect('/backup/checklist')->with('alert-warning', 'Please select a branch.');
    }

    
  	$setslps = $this->setslp->skipCache()->monthlyLogs($date, $branch);

  	if($request->has('debug'))
  		return $setslps;
  	
  	return view('docu.setslp.checklist')
  					->with('date', $date)
  					->with('branches', $bb)
  					->with('branch', $branch)
  					->with('setslps', $setslps);

		
	}

	public function getChecklist2(Request $request) {

		$bb = $this->branch
  						->orderBy('code')
  						->getByCriteria(new ActiveBranch)
  						->all(['code', 'descriptor', 'id']);
		$date = carbonCheckorNow($request->input('date'));

		if(!$request->has('branchid') && !isset($_GET['branchid'])) {
      return view('docu.setslp.checklist')
						->with('date', $date)
						->with('setslps', null)
						->with('branches', $bb)
						->with('branch', null);
    } 


    if(!is_uuid($request->input('branchid'))
    || !in_array(strtoupper($request->input('branchid')),  $this->branch->all()->pluck('id')->all())) 
    {
      return redirect('/setslp/checklist')->with('alert-warning', 'Please select a branch.');
    } 

    try {
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    } catch (Exception $e) {
      return redirect('/backup/checklist')->with('alert-warning', 'Please select a branch.');
    }

    $fr = $date->firstOfMonth();
  	$to = $date->copy()->lastOfMonth();

    $setslps = $this->setslp->branchByDR($fr, $to, $branch->id);
    $dss = $this->ds->getByBranchDate($fr, $to, $branch->id, ['date', 'sales', 'depo_cash', 'depo_check']);


    $arr = [];
    $gt = [];
    for ($i=0; $i < $date->daysInMonth; $i++) { 

  		$date = $fr->copy()->addDays($i);

  		$arr[$i]['date'] = $date;
  		$arr[$i]['depo_totamt'] = 0;
  		$arr[$i]['pos_totamt'] = 0;
  		$arr[$i]['depo_totcnt'] = 0;

  		$type = [];
  		for ($j=0; $j<3; $j++) {
  			$fd = $setslps->filter(function ($item) use ($date, $j){
        				return $item->date->format('Y-m-d') == $date->format('Y-m-d') && $item->type==$j
          			? $item : null;
    					})->all();
  			
	  		if (count($fd)>0) {
	  			$type[$j]['slips'] = $fd;
	  			$amt = 0;
	  			foreach ($fd as $key => $slip) {
	  				$amt += $slip->amount;
	  				$arr[$i]['depo_totcnt']++;
	  			}
	  			$type[$j]['amount'] = $amt;
	  			$arr[$i]['depo_totamt'] += $amt;
	  		} else
	  			$type[$j]['slips'] = false;
  		}
    	$arr[$i]['depo_type'] = $type;

    	$pos = [];
    	$ds = $dss->filter(function ($item) use ($date){
        				return $item->date->format('Y-m-d') == $date->format('Y-m-d')
          			? $item : null;
    					})->first();

    	if (is_null($ds)) {
    		$pos[0]['amount'] = false;
    		$pos[1]['amount']	= false;
    	} else {
    		$pos[0]['amount'] = $ds->depo_cash>0 ? $ds->depo_cash:false;
    		$pos[1]['amount']	= $ds->depo_check>0 ? $ds->depo_check:false;

    		if ($pos[0]['amount'])
    			$arr[$i]['pos_totamt'] += $pos[0]['amount'];

    		if ($pos[1]['amount'])
    			$arr[$i]['pos_totamt'] += $pos[1]['amount'];

    	}
    	$arr[$i]['pos'] = $pos;
  		
  	}

  	//return $arr;


  	if($request->has('debug'))
  		return $arr;
  	
  	return view('docu.setslp.checklist2')
  					->with('date', $fr)
  					->with('branches', $bb)
  					->with('branch', $branch)
  					->with('datas', $arr);

		
	}

	public function getAction($id=null, $action=null, $p=null) {
		//if(!is_uuid($id))
		//	return redirect('/setslp/log');
		

		if (strtolower($action)==='edit' && is_uuid($id) && is_null($p))
			return $this->editsetslp($id);
		elseif (is_uuid($id) && is_null($action) && is_null($p))
			return $this->viewsetslp($id);
		//elseif (strlen($id)==3)
		else
			return $this->getSetslpFileSystem($id, $action, $p);
		//else
		//	abort('404');
	}

	private function getSetslpFileSystem($id, $action, $p) { 

		$paths = [];
		$r = $this->files->folderInfo('SETSLP');
		foreach ($r['subfolders'] as $path => $folder) {
			$s = $this->files->folderInfo($path);
			foreach ($s['subfolders'] as $key => $value) {
				$paths[$key] = $value;
				//array_push($paths, $value);
			}
		}

		


		if (is_null($id) && is_null($action) && is_null($p))  {

			$z = array_unique($paths);
			asort($z);

			$data = [
				'folder' 			=> "/setslp",
				'folderName' 	=> 'SETSLP',
				'breadcrumbs' => [
					'/' 				=> "Storage",
				],
				'subfolders'	=> $z,
				'files'				=> []
			];
		
		} elseif ((!is_null($id) && is_null($action) && is_null($p)) && in_array(strtoupper($id), $paths))  {

			$dirs = [];

			foreach ($r['subfolders'] as $path => $folder) {
				if($this->files->exists($path.DS.strtoupper($id)))
					$dirs[$path] = $folder;
			}

			$data = [
				'folder' 			=> "/setslp/".strtoupper($id),
				'folderName' 	=> strtoupper($id),
				'breadcrumbs' => [
					'/' 				=> "Storage",
					'/setslp'		=> "setslp",
				],
				'subfolders'	=> $dirs,
				'files'				=> []
			];


		} elseif (in_array(strtoupper($id), $paths) && (!is_null($action) && is_year($action)) && is_null($p))  {

			$root = $this->files->folderInfo('setslp/'.$action.'/'.strtoupper($id));
			$data = [
				'folder' 			=> "/setslp/".strtoupper($id).'/'.$action,
				'folderName' 	=> $action,
				'breadcrumbs' => [
					'/' 				=> "Storage",
					'/setslp'		=> "setslp",
					'/setslp/'.strtoupper($id) 	=> strtoupper($id),
				],
				'subfolders'	=> $root['subfolders'],
				'files'				=> $root['files']
			];

		} elseif (in_array(strtoupper($id), $paths) && (!is_null($action) && is_year($action)) && (!is_null($p) && is_month($p)))  {

			$root = $this->files->folderInfo('setslp/'.$action.'/'.strtoupper($id).'/'.$p);
			$data = [
				'folder' 			=> "/setslp/".strtoupper($id).'/'.$action.'/'.$p,
				'folderName' 	=> $p,
				'breadcrumbs' => [
					'/' 				=> "Storage",
					'/setslp'		=> "setslp",
					'/setslp/'.strtoupper($id) 	=> strtoupper($id),
					'/setslp/'.strtoupper($id).'/'.$action 	=> $action,
				],
				'subfolders'	=> $root['subfolders'],
				'files'				=> $root['files']
			];
			
			

		} else 
			return abort('404');
		
		//return $data;
		return view('docu.setslp.filelist')->with('data', $data);

	}

	private function getSetslpFileSystems($id, $action, $p) {
		$branch = $this->branch->findWhere(['code'=>$id])->first();

			$paths = [];
		if (is_null($branch) && is_null($action) && is_null($p))  {

				//if (is_null($branch) && !is_null($id)) // with $brancid but no record found
				//	return abort('404');
	
			$setslp_root = $this->files->folderInfo('SETSLP');
			foreach ($setslp_root['subfolders'] as $path => $folder) {
				$x = $this->files->folderInfo($path);
				foreach ($x['subfolders'] as $key => $value) {
					$paths[$key] = $value;
					//array_push($paths, $value);
				}
			}

			$z = array_unique($paths);
			asort($z);

			$data = [
				'folder' 			=> "/setslp",
				'folderName' 	=> 'SETSLP',
				'breadcrumbs' => [
					'/' 				=> "Storage",
				],
				'subfolders'	=> $z,
				'files'				=> []
			];

		} else {


			$dirs = [];

			foreach ($setslp_root['subfolders'] as $path => $folder) {
				if($this->files->exists($path.DS.strtoupper($branch->code)))
					$dirs[$path] = $folder;

					$x = $this->files->folderInfo($path);
					array_push($paths, $x['subfolders']);
			}


			if (!is_null($id) && is_null($action) && is_null($action))	{
				$data = [
					'folder' 			=> "/setslp/".$branch->code,
					'folderName' 	=> $branch->code,
					'breadcrumbs' => [
						'/' 				=> "Storage",
						'/setslp'		=> "setslp",
					],
					'subfolders'	=> $dirs,
					'files'				=> []
				];
				//return $data = $this->files->folderInfo($branch->code);
			}	else if (in_array($action, $dirs) && is_null($p)) {
				$d = $this->files->folderInfo(array_search($action, $dirs).'/'.$branch->code);
				$data = [
					'folder' 			=> "/setslp/".$branch->code."/".$action,
					'folderName'  => $action,
					'breadcrumbs' => [
						'/' 				=> "Storage",
						'/setslp'   => "setslp",
					],
					'subfolders' 	=> $d['subfolders'],
					'files' 			=> $d['files']
				];
			}	elseif (in_array($action, $dirs) && is_month($p)) {
				$d = $this->files->folderInfo(array_search($action, $dirs).'/'.$branch->code.'/'.$p);
				$data = [
					'folder' 			=> "/setslp/".$branch->code."/".$action."/".$p,
					'folderName'  => $p,
					'breadcrumbs' => [
						'/' 				=> "Storage",
						'/setslp' 	=> "setslp",
						'/setslp/'.$branch->code => $branch->code,
						'/setslp/'.$branch->code.'/'.$action => $action,
					],
					'subfolders' 	=> $d['subfolders'],
					'files' 			=> $d['files']
				];
			} else {
				return 'fasfa';// abort('404');
			}

		}

		//return $data;
		
		return view('docu.setslp.filelist')->with('data', $data);
	}


	private function verify($id, $userid, $matched=0) {
		return $this->setslp->update([
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



	private function viewsetslp($id) {
		$setslp = $this->setslp->find($id);
		if(!$setslp->verified)
			if($this->checkVerify($setslp->id))
				return $this->viewsetslp($id);
		return view('docu.setslp.view', compact('setslp'));
	}

	private function editsetslp($id) {
		$setslp = $this->setslp->find($id);
		
		if(($setslp->verified || $setslp->matched) && (!request()->has('edit')))
			return $this->viewsetslp($id);
		return view('docu.setslp.edit', compact('setslp'));
	}

	public function getImage(Request $request, $filename) {

		$id = explode('.', $filename);

		if(!is_uuid($id[0]))
			return abort(404);

		$d = $this->setslp
				->skipCache()
				->with(['branch'=>function($query){
        	$query->select(['code', 'descriptor', 'id']);
      	}])
				->find($id[0]);

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

	private function countFilenameByDate($date, $time, $type) {
  	$d = $this->setslp->findWhere(['date'=>$date, 'time'=>$time, 'terminal_id'=>$type]);
		$c = intval(count($d));
  	if ($c>1)
			return $c;
		return false;
  }

  private function moveUpdatedFile($o, $n) {
  	if ($o->date!=$n->date || $o->time!=$n->time || $o->terminal_id!=$n->terminal_id) {
			
			$br = strtoupper($o->branch->code);
			$old_path = 'SETSLP'.DS.$o->date->format('Y').DS.$br.DS.$o->date->format('m').DS.$o->filename;
			$ext = strtolower(pathinfo($o->filename, PATHINFO_EXTENSION));
			switch ($n->terminal_id) {
				case 1:
					$type = 'BDO';
					break;
				case 2:
					$type = 'RCBC';
					break;	
				case 3:
					$type = 'HSBC';
					break;				
				default:
					$type = 'X';
					break;
			}
			
			
			if ($this->files->exists($old_path)) {
				$date = carbonCheckorNow($n->date->format('Y-m-d').' '.$n->time);

				$cnt = $this->countFilenameByDate($date->format('Y-m-d'), $date->format('H:i:s'), $n->terminal_id);
				if ($cnt)
					$filename = 'SETSLP '.$br.' '.$date->format('Ymd His').' '.$type.'-'.$cnt.'.'.$ext;
				else
					$filename = 'SETSLP '.$br.' '.$date->format('Ymd His').' '.$type.'.'.$ext;

				$new_path = 'SETSLP'.DS.$date->format('Y').DS.$br.DS.$date->format('m').DS.$filename; 

				try {
	     		$this->files->moveFile($this->files->realFullPath($old_path), $new_path, true); // false = override file!
		    } catch(Exception $e) {
					return false;
		    }
				return $filename;
			}
			return false;
		} else
			return false;
  }

	public function put(Request $request) {
		//return $request->all();
		$rules = [
			'date'				=> 'required|date',
			'time'				=> 'required',
			'amount'			=> 'required',
			'cashier'			=> 'required',
			'terminal_id'	=> 'required',
			'id'					=> 'required',
		];

		$validator = app('validator')->make($request->all(), $rules);

		if ($validator->fails()) 
			return redirect()->back()->withErrors($validator);
		
		$o = $this->setslp->with(['branch'=>function($query){
     		$query->select(['code', 'descriptor', 'id']);
     	}])->find($request->input('id'));
		
		if(!is_null($o)) {
			
			$d = $this->setslp->update([
				'date' 				=> request()->input('date'),
	    	'time' 				=> request()->input('time'),
	    	'terminal_id' => request()->input('terminal_id'),
	    	'amount' 			=> str_replace(",", "", request()->input('amount')),
	    	'cashier' 		=> $request->input('cashier'),
	    	'remarks' 		=> $request->input('notes'),
	    	'updated_at' 	=> c()
			], $o->id);

			$filename = $this->moveUpdatedFile($o, $d);
			if ($filename!==false) {
				$d = $this->setslp->update([
					'filename'		=> $filename,
		    	'updated_at' 	=> c()
				], $o->id);
			}

			array_forget($o, 'branch');
			//return [$o->toArray(), $d->toArray()];
			$arr = array_diff($o->toArray(), $d->toArray());
			array_forget($arr, 'updated_at');
			
			//if (app()->environment()==='production')
			//	event(new setslpChange($o, $d, $arr));

			return redirect('/setslp/'.$d->lid())
							->with('alert-success', 'Card settlement slip is updated!');
		}

		return redirect()->back()->withErrors('Card settlement Slip not found!');
	}



	private function getPath($d) {
		return 'SETSLP'.DS.$d->date->format('Y').DS.strtoupper($d->branch->code).DS.$d->date->format('m').DS.$d->filename;
	}

	public function delete(Request $request) {

		$validator = app('validator')->make($request->all(), ['id'=>'required'], []);

		if ($validator->fails()) 
			return redirect()->back()->withErrors($validator);

		$setslp = $this->setslp->find($request->input('id'));

		if (is_null($setslp))
			return redirect()->back()->withErrors('Card settlement slip not found!');

		if (!$setslp->isDeletable())
			return redirect()->back()->withErrors($setslp->fileUpload->filename.' card settlement slip is not deletable, already verified!');

		if ($this->setslp->delete($setslp->id)) {
			
			if ($this->files->exists($this->getPath($setslp)))
				$this->files->deleteFile($this->getPath($setslp));

			if (app()->environment()==='production')
				event(new setslpDelete($setslp->toArray()));

			return redirect('/setslp/log')
							->with('setslp.delete', $setslp)
							->with('alert-important', true);
		}
		return redirect()->back()->withErrors('Error while deleting record!');
	}


	public function getDownload(Request $request, $p1=NULL, $p2=NULL, $p3=NULL, $p4=NULL){
   
    if(is_null($p2) || is_null($p2) || is_null($p3) || is_null($p4)){
    	return abort('404');
    }

    $path = 'setslp/'.$p1.'/'.$p2.'/'.$p3.'/'.$p4;

    if (!in_array($request->user()->username, ['jrsalunga', 'admin']))
		logAction('backup:download', 'user:'.$request->user()->username.' '.$path);

	try {
	
		$file = $this->files->get($path);
		$mimetype = $this->files->fileMimeType($path);

    $response = \Response::make($file, 200);
	 	$response->header('Content-Type', $mimetype);
  	
  	if ($request->has('download') && $request->input('download')=='true')
  		$response->header('Content-Disposition', 'attachment; filename="'.$p4.'"');

	  return $response;
	} catch (\Exception $e) {
		return abort('404');
	}
  }






	






}