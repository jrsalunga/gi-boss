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

		$this->disk = new StorageRepository($mimeDetect, 'pos.'.app()->environment());
		$this->repository = $backuprepository;
	
		
	}

	

	public function index(Request $request) {
		$folder = '';
		return $this->disk->folderInfo($folder);
		return $request->all();
	}


	public function getStorage(Request $request, $p1=NULL, $p2=NULL, $p3=NULL) {
		$folder = $p1.'/'.$p2.'/'.$p3;
		return dd($this->disk->disk);

		$data = $this->disk->folderInfo($folder);
		//return $data;
		//return dd(count($data['breadcrumbs']));
		return view('backup.filelist')->with('data', $data);
		
		return dd($this->disk);
		return $request->all();
	}





  public function getDownload(Request $request, $p1=NULL, $p2=NULL, $p3=NULL, $p4=NULL){
    
    if(is_null($p2) || is_null($p2) || is_null($p3) || is_null($p4)){
    	throw new Http404("Error Processing Request");
    }

    $path = $p2.'/'.$p3.'/'.$p4;

		//$storage = $this->getStorageType($path);

		$file = $this->disk->get($path);
		$mimetype = $this->disk->fileMimeType($path);

    $response = \Response::make($file, 200);
	 	$response->header('Content-Type', $mimetype);
  	$response->header('Content-Disposition', 'attachment; filename="'.$p5.'"');

	  return $response;
  }
}