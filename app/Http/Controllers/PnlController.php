<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\Purchase2Repository as PurchRepo;
use App\Repositories\SalesmtdRepository as SalesRepo;

class PnlController extends Controller
{

	protected $dr;
	protected $salesRepo;
	protected $purchRepo;

	public function __construct(DateRange $dr, SalesRepo $salesRepo, PurchRepo $purchRepo) {
		$this->dr = $dr;
		$this->salesRepo = $salesRepo;
		$this->purchRepo = $purchRepo;
	}


	public function getMonth(Request $request) {
		$this->dr->setMode('monthly');
		return dd($this->dr);
	}




}