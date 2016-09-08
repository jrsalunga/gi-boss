<?php namespace App\Http\Controllers;

use Datatables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;

class MasterfilesController extends Controller {

	protected $datatables_data;

	public function __construct() {
		
	}

	private function validateToRepository($table=null) {
		if(is_null($table))
			return abort('404');

		$model = 'App\\Models\\'.ucwords($table);

		if(!class_exists($model))
  		return abort('404');
		
		$repo = 'App\\Repositories\\'.ucwords($table).'Repository';
		if(!class_exists($repo))
  		return abort('404');	

  	return app()->make($repo);
	}

	public function setDatatablesData($data) {
		$this->datatables_data = $data;
	}

	public function getDatatablesData() {
		return $this->datatables_data;
	}

	public function getIndex(Request $request, $table=null) {
		return view('masterfiles.index');
	}

	public function getController(Request $request, $table=null) {

		$repo = $this->validateToRepository($table);

		//$this->setDatatablesData($repo->with('company')->all());
		$this->setDatatablesData(Datatables::of($repo->with('company')->all())->make(true));

		//return view('masterfiles.index');
		return $this->getDatatablesData();
	}




}