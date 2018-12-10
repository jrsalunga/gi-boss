<?php namespace App\Http\Controllers;

use DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reports\CompcatPurchase;


class ReportsController extends Controller
{

	protected $ctrlCompcatPurchase;

	public function __construct(CompcatPurchase $ctrlCompcatPurchase) {
		$this->ctrlCompcatPurchase = $ctrlCompcatPurchase;
	}



	public function getCompcatPurchase(Request $request) {
		return $this->ctrlCompcatPurchase->getCompcatPurchase($request);
	}

	
	

	

  


}