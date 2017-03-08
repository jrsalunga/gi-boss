<?php namespace App\Http\Controllers;

use File;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Dflydev\ApacheMimeTypes\PhpRepository;
use Illuminate\Filesystem\Filesystem;
use App\Http\Controllers\Controller;
use App\Repositories\BackupRepository;
use App\Models\Backup;
use App\Models\Branch;
use App\Repositories\StorageRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException as Http404;
use App\Repositories\BranchRepository;
use Illuminate\Support\Collection;
use Illuminate\Container\Container as App;
use App\Repositories\Criterias\ActiveBranchCriteria as ActiveBranch;
use ZipArchive;



class BackupController extends Controller 
{

	protected $file;
	protected $disk;
	protected $pos;
	protected $fs;
	protected $branch;
	protected $mime;
	protected $backup;
	protected $repository;
	public $override = false;

	public function __construct(Request $request, PhpRepository $mimeDetect, BackupRepository $backuprepository){

		$this->disk = new StorageRepository($mimeDetect, 'backup.'.app()->environment());
		$this->file = new StorageRepository($mimeDetect, 'files.'.app()->environment());
		$this->repository = $backuprepository;
		$this->branch = new BranchRepository(app(), new Collection);
	}

	

	public function index(Request $request) {
		$folder = '';
		return $this->disk->folderInfo($folder);
		return dd($this->disk->disk);
		return $request->all();
	}


	public function getStorage(Request $request, $p1=NULL, $p2=NULL, $p3=NULL) {
		$folder = $p1.'/'.$p2.'/'.$p3;

		$data = $this->disk->folderInfo($folder);
		//return $data;
		//return dd(count($data['breadcrumbs']));
		return view('backup.filelist')->with('data', $data);
		
		return dd($this->disk);
		return $request->all();
	}

	public function getFileStorage(Request $request, $p1=NULL, $p2=NULL, $p3=NULL) {
		$folder = $p1.'/'.$p2.'/'.$p3;

		$data = $this->file->folderInfo($folder);

	}


	//storage/log
	public function getHistory(Request $request) {
		/*
		$this->repository->with(['branch'=>function($query){
        $query->select(['code', 'descriptor', 'id']);
      }])->scopeQuery(function($query){
	   	 return $query->orderBy('uploaddate','desc');
			})->all();
		*/
		$backups = $this->repository->skipCache()->scopeQuery(function($query){
	   	return $query->orderBy('uploaddate','desc');
			})->paginate(10, $columns = ['*']);
		return view('backup.index')->with('backups', $backups);
	}

	//backup/delinquent
	public function getDelinquent(Request $request){
		$branchs =  $this->branch->active()->all();
		//$branchs = Branch::orderBy('code')->get(['code', 'descriptor', 'id']);
	
		$arr = [];
		
		foreach ($branchs as $key => $branch) {
			$backup = Backup::where('branchid', $branch->id)
									->where('processed', 1)
									->orderBy('year', 'DESC')
									->orderBy('month', 'DESC')
									->orderBy('filename', 'DESC')
									->first(['filename', 'uploaddate']);
			//$arr[$key]['branch'] = $branch;
			//$arr[$key]['backup'] = $backup;
			
			array_push($arr, [
				'code'				=> $branch->code,
				'descriptor' 	=> $branch->descriptor,
				'branchid' 		=> $branch->id,
				'filename' 		=> is_null($backup) ? '':$backup->filename,
				'uploaddate' 	=> is_null($backup) ? '':$backup->uploaddate,
				'date' 				=> is_null($backup) ? '':$backup->uploaddate->format('Y-m-d H:i:s'),
			]);
		}

		$arr = array_values(array_sort($arr, function ($value) {
    	return $value['date'];
		}));

		return view('backup.delinquent')->with('backups', collect($arr));
		return dd(collect($arr));
	}



	public function delinquent(Request $request){
		$branchs = Branch::orderBy('code')->get(['code', 'descriptor', 'id']);
	
		$arr = [];
		$arr_wd = [];
		$arr_wo = [];
		
		foreach ($branchs as $key => $branch) {
			$backup = Backup::where('branchid', $branch->id)
									->where('processed', 1)
									->orderBy('year', 'DESC')
									->orderBy('month', 'DESC')
									->orderBy('filename', 'DESC')
									->first(['filename', 'uploaddate']);
			//$arr[$key]['branch'] = $branch;
			//$arr[$key]['backup'] = $backup;
			if(is_null($backup)) {
				array_push($arr_wo, [
					'code'				=> $branch->code,
					'descriptor' 	=> $branch->descriptor,
					'branchid' 		=> $branch->id,
					'filename' 		=> null,
					'uploaddate' 	=> null,
					'date' 				=> null,
				]);
			} else {
				array_push($arr_wd, [
					'code'				=> $branch->code,
					'descriptor' 	=> $branch->descriptor,
					'branchid' 		=> $branch->id,
					'filename' 		=> is_null($backup) ? '':$backup->filename,
					'uploaddate' 	=> is_null($backup) ? '':$backup->uploaddate,
					'date' 				=> is_null($backup) ? '':$backup->uploaddate->format('Y-m-d H:i:s'),
				]);
			}

		}

		$arr_wd = array_values(array_sort($arr_wd, function ($value) {
    	return $value['date'];
		}));

		$arr_wo = array_values(array_sort($arr_wo, function ($value) {
    	return $value['code'];
		}));


		//return view('backup.delinquent')->with('backups', collect($arr));
		//$arr = [$arr_wo, $arr_wd];

		return collect([$arr_wo, $arr_wd]);
	}





  public function getDownload(Request $request, $p1=NULL, $p2=NULL, $p3=NULL, $p4=NULL){
    
    if(is_null($p2) || is_null($p2) || is_null($p3) || is_null($p4)){
    	throw new Http404("Error Processing Request");
    }

    $path = $p1.'/'.$p2.'/'.$p3.'/'.$p4;

		logAction('backup:download', 'user:'.$request->user()->username.' '.$path);
		//return dd($this->disk->realFullPath(''));
		$file = $this->disk->get($path);
		$mimetype = $this->disk->fileMimeType($path);


    $response = \Response::make($file, 200);
	$response->header('Content-Type', $mimetype);
  	$response->header('Content-Disposition', 'attachment; filename="'.$p4.'"');

	  return $response;
  }





  public function getChecklist(Request $request) {

  	$bb = $this->branch
  						->orderBy('code')
  						->getByCriteria(new ActiveBranch)
  						->all(['code', 'descriptor', 'id']);

  	$date = carbonCheckorNow($request->input('date'));

  	if(!$request->has('branchid') && !isset($_GET['branchid'])) {
      return view('backup.checklist')
  					->with('date', $date)
  					->with('branches', $bb)
  					->with('branch', null)
  					->with('backups', null);
    } 
    
    if(!is_uuid($request->input('branchid'))
    || !in_array(strtoupper($request->input('branchid')),  $this->branch->all()->pluck('id')->all())) 
    {
      return redirect('/backup/checklist')->with('alert-warning', 'Please select a branch.');
    } 

    try {
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    } catch (Exception $e) {
      return redirect('/backup/checklist')->with('alert-warning', 'Please select a branch.');
    }

  	$backups = $this->repository->monthlyLogs($date, $branch);
  	
  	return view('backup.checklist')
  					->with('date', $date)
  					->with('branches', $bb)
  					->with('branch', $branch)
  					->with('backups', $backups);

  }


  public function getBatchDownload(Request $request) {


  	return view('backup.batch-download');
  }
  public function postBatchDownload(Request $request) {

  	$branches = $this->branch->skipCache()->active()->all(['code'])->pluck('code')->toArray();

  	//$branches = ['MOA', 'ANG', 'GLV'];

  	$date = c($request->input('date'));
  	$path = public_path('downloads/'.$date->format('Ymd').'.ZIP');

  	//$zip = new ZipArchive;
		//$res = $zip->open($path, ZipArchive::CREATE);
		//$zip->addFromString('test.txt', 'file content goes here');
		//$zip->addFile('data.txt', 'entryname.txt');
		//$zip->close();
		//return 1;

		$zip = new ZipArchive;
		$res = $zip->open($path, ZipArchive::CREATE);
  	
	  $ctr=0;

	  $paths = [];
		$r = $this->disk->folderInfo('.');
		foreach ($r['subfolders'] as $path => $folder) {
			$filename = 'GC'.$date->format('mdy').'.ZIP';
			$file = $folder.DS.$date->format('Y').DS.$date->format('m').DS.$filename;
			
			if ($this->disk->exists($file)) {
				$paths[$folder] = $file;
				$ctr++;
				unset($branches[array_search($folder, $branches)]);
				//array_forget($branches, $folder);
				//if (file_exists($this->disk->realFullPath($file)))
				//$paths[$folder] = $this->disk->realFullPath($file);
			}

			if ($res == true && $ctr>=1) {
    		$zip->addFile($this->disk->realFullPath($file), $folder.DS.$filename);
			}

		}

		


		if ($res === true && $ctr>=1) {
		  $zip->addFromString('info.txt', $filename .' backups downloaded from Giligan\'s Boss Module. ' .session('user.fullname').' - '.c()->format('Y-m-d H:i:s'));
		  $zip->close();
			return redirect('/storage/batch-download')
						->with('file', $date->format('Ymd').'.ZIP')
						->with('branches', $branches)
						->with('count', $ctr);
		} else {
			return redirect('/storage/batch-download')
						->withErrors('No backups found on this date!');
			
		}

	
  }
}