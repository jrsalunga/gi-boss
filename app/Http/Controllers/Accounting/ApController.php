<?php namespace App\Http\Controllers\Accounting;
use stdClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\Purchase2Repository as Purchase;

class ApController extends Controller
{

	protected $purchase;

	public function __construct(DateRange $dr, Purchase $purchase) {
    $this->dr = $dr;
		$this->purchase = $purchase;
	}

	

	

  


}