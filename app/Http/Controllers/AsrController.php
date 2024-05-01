<?php namespace App\Http\Controllers;
use URL;
use Event;
use StdClass;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\StorageRepository;
use Dflydev\ApacheMimeTypes\PhpRepository;

class AsrController extends Controller { 

	public function __construct() {
		$this->files = new StorageRepository(new PhpRepository, 'files.'.app()->environment());
	}

	public function getAction($id=null, $action=null, $p=null) {
		return $this->getFileSystem($id, $action, $p);
	}

	private function getFileSystem($id, $action, $p) { 

		$paths = [];
		$r = $this->files->folderInfo('ASR');
		foreach ($r['subfolders'] as $path => $folder) {
			$s = $this->files->folderInfo($path);
			foreach ($s['subfolders'] as $key => $value) {
				$paths[$key] = $value;
			}
		}

		if (is_null($id) && is_null($action) && is_null($p))  {

			$z = array_unique($paths);
			asort($z);

			$data = [
				'folder' 			=> "/ASR",
				'folderName' 	=> 'ASR',
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
				'folder' 			=> "/ASR/".strtoupper($id),
				'folderName' 	=> strtoupper($id),
				'breadcrumbs' => [
					'/' 				=> "Storage",
					'/asr'		=> "ASR",
				],
				'subfolders'	=> $dirs,
				'files'				=> []
			];


		} elseif (in_array(strtoupper($id), $paths) && (!is_null($action) && is_year($action)) && is_null($p))  {

			$root = $this->files->folderInfo('ASR/'.$action.'/'.strtoupper($id));
			$data = [
				'folder' 			=> "/ASR/".strtoupper($id).'/'.$action,
				'folderName' 	=> $action,
				'breadcrumbs' => [
					'/' 				=> "Storage",
					'/asr'		=> "ASR",
					'/asr/'.strtoupper($id) 	=> strtoupper($id),
				],
				'subfolders'	=> $root['subfolders'],
				'files'				=> $root['files']
			];

		} elseif (in_array(strtoupper($id), $paths) && (!is_null($action) && is_year($action)) && (!is_null($p) && is_month($p)))  {

			$root = $this->files->folderInfo('ASR/'.$action.'/'.strtoupper($id).'/'.$p);
			$data = [
				'folder' 			=> "/ASR/".strtoupper($id).'/'.$action.'/'.$p,
				'folderName' 	=> $p,
				'breadcrumbs' => [
					'/' 				=> "Storage",
					'/asr'		=> "ASR",
					'/asr/'.strtoupper($id) 	=> strtoupper($id),
					'/asr/'.strtoupper($id).'/'.$action 	=> $action,
				],
				'subfolders'	=> $root['subfolders'],
				'files'				=> $root['files']
			];
			
			

		} else 
			return abort('404');
		
		return view('docu.asr.filelist')->with('data', $data);
	}
	
	private function getPath($d) {
		return 'ASR'.DS.$d->date->format('Y').DS.strtoupper($d->branch->code).DS.$d->date->format('m').DS.$d->filename;
	}

	public function getDownload(Request $request, $p1=NULL, $p2=NULL, $p3=NULL, $p4=NULL){
   
    if(is_null($p2) || is_null($p2) || is_null($p3) || is_null($p4)){
    	return abort('404');
    }

    $path = 'ASR/'.$p1.'/'.$p2.'/'.$p3.'/'.$p4;

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