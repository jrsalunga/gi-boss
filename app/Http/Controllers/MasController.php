<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\EmployeeRepository as EmployeeRepo;


class MasController extends Controller
{

	protected $dr;
	protected $employee;

	public function __construct(DateRange $dr, EmployeeRepo $employeeRepo) {
		$this->dr = $dr;
		$this->employee = $employeeRepo;
	}


	

  


}