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



class BackupController extends Controller 
{

	protected $files;
	protected $pos;
	protected $fs;
	protected $branch;
	protected $mime;
	protected $backup;
	public $override = false;

	public function __construct(Request $request, PhpRepository $mimeDetect, BackupRepository $backuprepository){

		$this->disk = new StorageRepository($mimeDetect, 'backup.'.app()->environment());
		$this->repository = $backuprepository;
	
		
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


	//backups/history
	public function getHistory(Request $request) {

		
		
		
		$this->repository->with(['branch'=>function($query){
        $query->select(['code', 'descriptor', 'id']);
      }])->scopeQuery(function($query){
	   	 return $query->orderBy('uploaddate','desc');
			})->all();
		
		$backups = $this->repository->paginate(10, $columns = ['*']);

		//return $backups;
		
		return view('backup.index')->with('backups', $backups);
	}

	public function getDelinquent(Request $request){
		$branchs = Branch::orderBy('code')->get(['code', 'descriptor', 'id']);
	
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





  public function getDownload(Request $request, $p1=NULL, $p2=NULL, $p3=NULL, $p4=NULL){
    
    if(is_null($p2) || is_null($p2) || is_null($p3) || is_null($p4)){
    	throw new Http404("Error Processing Request");
    }

    $path = $p1.'/'.$p2.'/'.$p3.'/'.$p4;

		logAction('backup:download', 'user:'.$request->user()->username.' '.$p4);

		$file = $this->disk->get($path);
		$mimetype = $this->disk->fileMimeType($path);

    $response = \Response::make($file, 200);
	 	$response->header('Content-Type', $mimetype);
  	$response->header('Content-Disposition', 'attachment; filename="'.$p4.'"');

	  return $response;
  }
}