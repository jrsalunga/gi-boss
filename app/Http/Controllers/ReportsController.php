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
use App\Repositories\Boss\BranchRepository as BranchRepo;
use App\Repositories\Criterias\ActiveBossBranchCriteria as ActiveBranch;
use App\Repositories\Criterias\OpenBossBranchCriteria as OpenBranch;
use App\Repositories\SetslpRepository as Setslp;
use App\Repositories\DepslipRepository as Depslip;
use App\Repositories\MonthlysalesRepository as MS;


class ReportsController extends Controller
{

	protected $ctrlCompcatPurchase;
  protected $cashAudit;
  protected $branch;
  protected $dr;
  protected $mCashAudit;
  protected $setslp;
  protected $depslip;
  protected $ms;

	public function __construct(DateRange $dr, CompcatPurchase $ctrlCompcatPurchase, CashAudit $cashAudit, BranchRepo $branch, MonthCashAudit $mCashAudit, Setslp $setslp, Depslip $depslip, MS $ms) {
		$this->ctrlCompcatPurchase = $ctrlCompcatPurchase;
    $this->dr = $dr;
    $this->cashAudit = $cashAudit;
    $this->mCashAudit = $mCashAudit;
    $this->branch = $branch;
    $this->setslp = $setslp;
    $this->depslip = $depslip;
    $this->ms = $ms;
    // $this->branch->pushCriteria(new ActiveBranch(['code', 'descriptor', 'id']));
    $this->bb = $this->getBranches();
	}

  private function getBranches() {
    return $this->branch->active(['code', 'descriptor', 'id'])->all();
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

  public function getDailyCashFlow(Request $request) {

    if ($request->has('date'))
      $date = carbonCheckorNow($request->input('date'));
    else 
      $date = c()->format('H')>20 ? c() : c()->subDay();

    $this->dr->date = $date;
    $this->dr->fr = $date;
    $this->dr->to = $date;

    $datas = [];
    
    foreach($this->branch->open(['code', 'descriptor', 'id'])->all() as $k => $branch) {
      $datas[$branch->code]['code'] = $branch->code;
      $datas[$branch->code]['branch'] = $branch->descriptor;
      $datas[$branch->code]['branch_id'] = $branch->id;

      $cash_audit = $this->cashAudit->findWhere(['branch_id'=>$branch->id, 'date'=>$date->format('Y-m-d')], 
                        ['csh_fwdd', 'deposit', 'csh_sale', 'chg_sale', 'csh_disb', 'csh_bal', 'csh_cnt', 'shrt_ovr', 'shrt_cumm', 'csh_out', 'col_ca', 'col_cas', 'tot_coll'])
                        ->first();

      if (is_null($cash_audit))
        $datas[$branch->code]['cash_audit'] = NULL;
      else {
        
        $cash_audit->change_fund = $cash_audit->csh_fwdd-$cash_audit->deposit;
        
        if ($cash_audit->csh_fwdd>0) {
          $cash_audit->csh_fwdd_pct = ($cash_audit->deposit/$cash_audit->csh_fwdd)*100;
          $cash_audit->change_fund_pct = ($cash_audit->change_fund/$cash_audit->csh_fwdd)*100;
        } else {
          $cash_audit->csh_fwdd_pct = 0;
          $cash_audit->change_fund_pct = 0;
        }
        
        $cash_audit->csh_in_out = $cash_audit->col_ca - $cash_audit->csh_out;
        $cash_audit->cash_total = $cash_audit->change_fund + $cash_audit->csh_sale + $cash_audit->csh_in_out;
        $cash_audit->pos_sales = $cash_audit->csh_sale + $cash_audit->chg_sale;

        $datas[$branch->code]['cash_audit'] = $cash_audit;
      }


      // $depslps = $this->depslip->findWhere(['branch_id'=>$branch->id, 'date'=>$date->format('Y-m-d')]);
      // $datas[$branch->code]['depo_error'] = false;
      // $datas[$branch->code]['depo_total'] = 0;
      // if (count($depslps)>0) {

      //   $datas[$branch->code]['depslps'] = $depslps;
      //   $datas[$branch->code]['depo_total'] = $depslps->sum('amount');

      //   if (!is_null($cash_audit))
      //     if ($datas[$branch->code]['depo_total']!=$cash_audit->deposit)
      //       $datas[$branch->code]['depo_error'] = true;
      // } else
      //   $datas[$branch->code]['depslps'] = NULL;
      
    };


    if ($request->has('raw'))
      return $datas;


    if (!is_null($branch) && !in_array($request->user()->id, ['41F0FB56DFA811E69815D19988DDBE1E', '11E943EA14DDA9E4EAAFBD26C5429A67'])) {
      $email = [
        'body' => $request->user()->name.' '.$date->format('Y-m-d')
      ];

      \Mail::queue('emails.notifier', $email, function ($m) {
        $m->from('giligans.app@gmail.com', 'GI App - Boss');
        $m->to('giligans.log@gmail.com')->subject('All Branch Cash Flow - '.rand());
      });
    }

    if($request->has('raw'))
      return $datas;


    return $this->setViewWithDR(view('report.dailycashflow')
                ->with('datas', $datas)
                ->with('cash_audit', $cash_audit));
  }


  public function getMonthlyCashFlow(Request $request) {

    $datas = $this->ms->allBranchMonthlyCashFlow($request->input('date'));

    return $this->setViewWithDR(view('report.cashflow-month')
                ->with('datas', $datas));
  }

 







  private function setViewWithDR($view){
    $response = new Response($view->with('dr', $this->dr));
    $response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
    $response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
    $response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
    return $response;
  }
}