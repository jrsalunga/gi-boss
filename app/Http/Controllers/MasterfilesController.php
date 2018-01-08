<?php namespace App\Http\Controllers;

use Datatables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\Criterias\LimitCriteria;

class MasterfilesController extends Controller {

	protected $datatables_data;
	protected $tables = ['branch','component', 'company'];

	public function __construct() {
		
	}

	private function isValidTable($table=null) {
		if(is_null($table))
			return false;
		$tb = strtolower($table);
		if(in_array($table, $this->tables))
			return $tb;
		else
			return false;
	} 

	private function validateToRepository($table) {

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

	public function getDatatableIndex(Request $request, $table=null) {
		return view('masterfiles.datatable');
	}

	public function getController(Request $request, $table=null) {

		$repo = $this->validateToRepository($table);

		//$this->setDatatablesData($repo->with('company')->all());
		$this->setDatatablesData(Datatables::of($repo->with('company')->all())->make(true));

		//return view('masterfiles.index');
		return $this->getDatatablesData();
	}


	public function getIndex(Request $request, $table=null) {

		$table = $this->isValidTable($table);
		if(!$table)
			return view('masterfiles.index')->with('tables', $this->tables);

		$datas = $this->getRepositoryData($request, $table);

		return view('masterfiles.branch', compact('datas'))->with('tables', $this->tables)->with('active', $table);
	}

	private function getRepositoryData(Request $request, $table) {

		$repository = $this->validateToRepository($table);

		//$repository->orderBy('code', 'asc')->orderBy('descriptor', 'asc');
		//$repository->skipCache(true);

		return $repository->skipCache()->order()->paginate($this->getLimit($request));
	}

	private function getLimit(Request $request, $limit = 10) {

		if( $request->has('limit')
		&& filter_var($request->input('limit'), FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range' => 100]]) ) 
		{
			return $request->input('limit');
		} else {
			return $limit;
		}
	}



}