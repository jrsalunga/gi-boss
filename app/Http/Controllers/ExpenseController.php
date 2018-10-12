<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\MonthExpenseRepository as mExpense;
use App\Repositories\ExpenseRepository as Expense;
use App\Repositories\BranchRepository as BranchRepo;
use App\Repositories\StockTransferRepository as Transfer;
use App\Repositories\MonthProdcatRepository as mProdcat;
use App\Repositories\MonthlySalesRepository as MS;

class ExpenseController extends Controller
{

	protected $dr;
	protected $transfer;
	protected $expense;
	protected $mExpense;
	protected $mProdcat;
	protected $branch;
	protected $bb;
	protected $ms;

	public function __construct(DateRange $dr, Expense $expense, mExpense $mExpense, Transfer $transfer, mProdcat $mProdcat, BranchRepo $branch, MS $ms) {
		$this->dr = $dr;
		$this->ms = $ms;
		$this->expense = $expense;
		$this->transfer = $transfer;
		$this->mExpense = $mExpense;
		$this->mProdcat = $mProdcat;
		$this->branch = $branch;
		$this->bb = $this->getBranches();
	}

	private function getBranches() {
		return $this->branch->orderBy('code')->all(['code', 'descriptor', 'id']);
	}


	public function getMonthFoodCostBreakdown(Request $request) {

		$date = carbonCheckorNow($request->input('date'));
		$this->dr->date = $date;
		$this->dr->fr = $date->copy()->startOfMonth();
		$this->dr->to = $date->copy()->endOfMonth();



		$ms = null;

		//return dd($this->dr);
			
		if ($request->has('branchid') && is_uuid($request->input('branchid')))
      $branch = $this->branch->find(strtolower($request->input('branchid')));
    else
      $branch = null;

    $datas = [];
    $fc_hist = [];
    if (!is_null($branch)) {

    	$exps = $this->expense->skipCache()->getCos();

    	$ms = $this->ms->skipCache()->findWhere(['date'=>$this->dr->to->format('Y-m-d'), 'branch_id'=>$branch->id], ['date', 'sales', 'food_sales', 'fc'])->first();

			$datas = $this->FCBreakdownData($branch, $exps);
    	$fc_hist = $this->getFCHist($branch, $exps);
							                 

		//return $fc_hist;
    }
    return $this->setViewWithDR(view('report.food-cost-breakdown')
                ->with('branches', $this->bb)
                ->with('hist', $fc_hist)
                ->with('datas', $datas)
                ->with('ms', $ms)
                ->with('branch', $branch));
	}

	private function getFCHist($branch, $exps) {
		$datas = [];
		$len = 5;
		$mfr = $this->dr->date->copy()->subMonths($len)->startOfMonth();
		$mto = $this->dr->date->copy()->endOfMonth();
		$mexps = $this->mExpense->skipCache()->branchIdByDr($mfr, $mto, $branch->id)->all();

		$mss = $this->ms->scopeQuery(function($query) use ($mfr, $mto) {
									return $query->whereBetween('date', [$mfr->format('Y-m-d'), $mto->format('Y-m-d')]);
								})->findWhere(['branch_id'=>$branch->id], ['date', 'sales', 'food_sales', 'fc']);

		foreach (range(0,$len) as $key => $value) {
			$date = $mfr->copy()->addMonth($key)->endOfMonth();
			$datas[$key]['date'] = $date;
			foreach ($exps as $k => $exp) {

				$f = $mexps->filter(function ($item) use ($exp, $date){
		      return $item->expense_id == $exp->id && $item->date->format('Y-m-d')==$date->format('Y-m-d')
		      	? $item : null;
		    });
		    $b = $f->first();
				$datas[$key]['data'][$k] = is_null($b) ? 0 : $b->tcost-$b->xfred;

		    

			}

			$g = $mss->filter(function ($item) use ($date){
	      return $item->date->format('Y-m-d')==$date->format('Y-m-d')
	      	? $item : null;
	    });
	    $c = $g->first();
			$datas[$key]['fc'] = is_null($c) ? 0 : $c->fc;
		}

		return $datas;
	}

	private function FCBreakdownData($branch, $exps) {

		$datas = [];

		
		$ids = $exps->pluck('id')->toArray();

		$mexps = $this->mExpense->skipCache()->sumCosByDr($branch->id, $this->dr->fr, $this->dr->to, $ids);
		$fsales = $this->mProdcat->skipCache()->sumSalesByProdcatDr($branch->id, $this->dr->fr, $this->dr->to, '6270B37CBDF211E6978200FF18C615EC');
	  $fs = $fsales->first();

		foreach ($exps as $x => $exp) {
			$f = $mexps->filter(function ($item) use ($exp){
	      return $item->expense_id == $exp->id
	      	? $item : null;
	    });
	    $b = $f->first();

	  	$datas[$x]['expensecode'] = $exp->code;
	  	$datas[$x]['expense'] = $exp->descriptor;
	  	$datas[$x]['expenseid'] = $exp->lid();
			
			if(is_null($b)) 
	  		$datas[$x]['purch'] = 0;
	  	else
	  		$datas[$x]['purch'] = $b->tcost;

			$trns = $this->transfer->skipCache()->getSumCosByDr($branch->id, $this->dr->fr, $this->dr->to, $exp->code);

			$c = $trns->first();

			if(is_null($c)) 
	  		$datas[$x]['trans'] = 0;
	  	else
	  		$datas[$x]['trans'] = is_null($c->tcost) ? 0 : $c->tcost;			
	  	
	  	$sales = is_null($fs->sales) ? 0 : $fs->sales;
	  	$datas[$x]['food_sales'] = $sales;
	  	$datas[$x]['net'] = $datas[$x]['purch'] - $datas[$x]['trans'];			
	  	$datas[$x]['pct'] = $datas[$x]['food_sales'] > 0 ? ($datas[$x]['net'] / $datas[$x]['food_sales']) * 100 : 0;			
		}
		return $datas;
	}

	private function setViewWithDR($view){
    $response = new Response($view->with('dr', $this->dr));
    $response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
    $response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
    $response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
    return $response;
  }

  


}