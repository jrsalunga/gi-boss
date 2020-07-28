<?php namespace App\Http\Controllers;

use DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reports\CompcatPurchase;
use App\Repositories\CashAuditRepository as CashAudit;
use App\Repositories\MonthCashAuditRepository as MonthCashAudit;
use App\Repositories\BranchRepository as BranchRepo;
use App\Repositories\Criterias\ActiveBranchCriteria as ActiveBranch;
use App\Repositories\SetslpRepository as Setslp;
use App\Repositories\DepslipRepository as Depslip;


class ReportsController extends Controller
{

	protected $ctrlCompcatPurchase;
  protected $cashAudit;
  protected $branch;
  protected $dr;
  protected $mCashAudit;
  protected $setslp;
  protected $depslip;

	public function __construct(DateRange $dr, CompcatPurchase $ctrlCompcatPurchase, CashAudit $cashAudit, BranchRepo $branch, MonthCashAudit $mCashAudit, Setslp $setslp, Depslip $depslip) {
		$this->ctrlCompcatPurchase = $ctrlCompcatPurchase;
    $this->dr = $dr;
    $this->cashAudit = $cashAudit;
    $this->mCashAudit = $mCashAudit;
    $this->branch = $branch;
    $this->setslp = $setslp;
    $this->depslip = $depslip;
    $this->bb = $this->getBranches();
    $this->branch->pushCriteria(new ActiveBranch);
	}

  private function getBranches() {
    return $this->branch->orderBy('code')->all(['code', 'descriptor', 'id']);
  }

	public function getCompcatPurchase(Request $request) {
		return $this->ctrlCompcatPurchase->getCompcatPurchase($request);
	}

  public function getCashAudit(Request $request) {
    $date = carbonCheckorNow($request->input('date'));
    $this->dr->date = $date;
    $this->dr->fr = $date;
    $this->dr->to = $date;
    
    $cash_audit = NULL;
    $month_cashaudit = NULL;
    $setslps = NULL;
    $depslps = NULL;
    $branch = null;
    $datas = [];

    if ($request->has('branchid') && is_uuid($request->input('branchid')))
      $branch = $this->branch->find(strtolower($request->input('branchid')));

    if (!is_null($branch)) {

      $cash_audit = $this->cashAudit->findWhere(['branch_id'=>$branch->id, 'date'=>$date->format('Y-m-d')])->first();
      //$month_cashaudit = $this->mCashAudit->findWhere(['branch_id'=>$branch->id, 'date'=>$date->copy()->endOfMonth()->format('Y-m-d')])->first();
      $month_cashaudit = $this->cashAudit->aggregateByDr($date->copy()->startOfMonth(), $date, $branch->id);

      $depslps = $this->depslip->findWhere(['branch_id'=>$branch->id, 'date'=>$date->format('Y-m-d')]);
      $setslps = $this->setslp->findWhere(['branch_id'=>$branch->id, 'date'=>$date->format('Y-m-d')]);
    }

    return $this->setViewWithDR(view('report.cash-audit')
                ->with('branches', $this->bb)
                ->with('cash_audit', $cash_audit)
                ->with('month_cashaudit', $month_cashaudit)
                ->with('datas', $datas)
                ->with('depslps', $depslps)
                ->with('setslps', $setslps)
                ->with('branch', $branch));
  }

11E8A4F31C1B0D85A7E0B72EBD84AEB5










  private function setViewWithDR($view){
    $response = new Response($view->with('dr', $this->dr));
    $response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
    $response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
    $response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
    return $response;
  }
}