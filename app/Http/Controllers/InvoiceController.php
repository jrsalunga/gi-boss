<?php namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\DailySalesRepository as DS;
use App\Repositories\Purchase2Repository as PurchaseRepo;
use App\Repositories\ApUploadRepository as ApUpload;

class InvoiceController extends Controller
{

	protected $dr;
	protected $ds;
  protected $purchase;
  protected $apUpload;

	public function __construct(DateRange $dr, DS $ds, PurchaseRepo $purchase, ApUpload $apUpload) {
    $this->dr = $dr;
		$this->ds = $ds;
    $this->purchase = $purchase;
    $this->apUpload = $apUpload;
	}


  public function getInvoice(Request $request) {

    $rules = [
      'date'      => 'required|date',
      'supprefno' => 'required',
      'branchid'  => 'required',
    ];

    $validator = app('validator')->make($request->all(), $rules);

    if ($validator->fails()) 
      return abort(404);
      // return view('invoice.view', compact('invoice', 'apus'))->withErrors($validator);

    $invoice = [];
    $apus = [];
    $purchases = $this->purchase->with(['component.compcat.expense', 'branch', 'supplier'])->findWhere(['supprefno'=>$request->input('supprefno'), 'date'=>$request->input('date'), 'branchid'=>$request->input('branchid')]);

    $where = [
      'branch_id'=>$request->input('branchid'),
      'date'=>$request->input('date')
    ];


    if (count($purchases)>0) {
      $invoice['date'] = c($request->date);
      $invoice['no'] = $request->supprefno;
      $invoice['total_amount'] = nf($purchases->sum('tcost'),2);
      $invoice['line'] = count($purchases);

      $invoice['save'] = $purchases[0]->save;
      $invoice['posted_at'] = is_iso_date($purchases[0]->posted_at) ? c($purchases[0]->posted_at) : NULL;
      $invoice['paytype'] = $purchases[0]->paytype;
      $invoice['terms'] = $purchases[0]->terms;

      $invoice['supplier'] = NULL;
      if(isset($purchases[0]->supplier)) {
        $invoice['supplier'] = $purchases[0]->supplier;
        $where['supplier_id'] = $purchases[0]->supplier->id;
      }

       $invoice['branch'] = NULL;
      if(isset($purchases[0]->branch))
        $invoice['branch'] = $purchases[0]->branch;

      $invoice['items'] = $purchases;
    } else {
      return abort('404');
    }

    array_push($where, ['refno', 'like', '%'.$request->supprefno.'%']);

    //return dd($where);

    $apus = $this->apUpload->skipCache()->with(['doctype', 'supplier'])->findWhere($where);
    if (count($apus)>1) {
      $where['amount'] = str_replace(',', '', $invoice['total_amount']);
      $apus = $this->apUpload->skipCache()->with(['doctype', 'supplier'])->findWhere($where);
    }

    if (count($apus)<=0) {
      $where['amount'] = str_replace(',', '', $invoice['total_amount']);
      unset($where['date']);
      $apus = $this->apUpload->skipCache()->with(['doctype', 'supplier'])->findWhere($where);
    }
    
    // return $apus;
    // return $invoice;

    return view('invoice.view', compact('invoice', 'apus'));

  }


  public function updateInvoice(Request $request) {

    $rules = [
      'to'        => 'required|date',
      'fr'        => 'required|date',
      'supprefno' => 'required',
      'branchid'  => 'required',
      'save'      => 'required',
    ];

    $validator = app('validator')->make($request->all(), $rules);

    if ($validator->fails()) 
      return redirect()->back()->withErrors($validator);


    $purchases = $this->purchase->findWhere(['supprefno'=>$request->input('supprefno'), 'date'=>$request->input('fr'), 'branchid'=>$request->input('branchid')]);
    if (count($purchases)<=0)
      return redirect()->back()->with('alert-error', 'No records found!')->with('alert-important', '');

    
    if (is_null($purchases[0]->posted_at)) { // change to new posting date    

      if ($request->input('save')==1) 
        $save = ['date'=>$request->input('to'), 'posted_at'=>$request->input('fr')];
      else
        $save = ['save'=>1, 'date'=>$request->input('to'), 'posted_at'=>$request->input('fr')];

    } else { // back to original posting date
      $save = ['date'=>$request->input('to'), 'posted_at'=>NULL];
    }

    $updated_purchases = DB::table('purchase')
                          ->where(['supprefno'=>$request->input('supprefno'), 'date'=>$request->input('fr'), 'branchid'=>$request->input('branchid')])
                          ->update($save);


    if ($updated_purchases>0) {

      event(new \App\Events\Process\AggregateComponentDaily(c($request->input('fr')), $request->input('branchid'))); // recompute Daily Component
      event(new \App\Events\Process\AggregateDailyExpense(c($request->input('fr')), $request->input('branchid'))); // recompute Daily Expense

      event(new \App\Events\Process\AggregateComponentMonthly(c($request->input('fr')), $request->input('branchid'))); // recompute Monthly Component
      event(new \App\Events\Process\AggregateMonthlyExpense(c($request->input('fr')), $request->input('branchid'))); // recompute Monthly Expense


      event(new \App\Events\Process\AggregateComponentDaily(c($request->input('to')), $request->input('branchid'))); // recompute Daily Component
      event(new \App\Events\Process\AggregateDailyExpense(c($request->input('to')), $request->input('branchid'))); // recompute Daily Expense

      event(new \App\Events\Process\AggregateComponentMonthly(c($request->input('to')), $request->input('branchid'))); // recompute Monthly Component
      event(new \App\Events\Process\AggregateMonthlyExpense(c($request->input('to')), $request->input('branchid'))); // recompute Monthly Expense


      

      return redirect('/invoice?branchid='.strtolower($request->input('branchid')).'&date='.$save['date'].'&supprefno='.$request->input('supprefno'))->with('alert-success', 'Invoice ('.$request->input('supprefno').') date has been updated.');
      return $updated_purchases;
    } else
      return redirect()->back()->with('alert-error', 'No records found to update.')->with('alert-important', '');
  }


  public function updateInvoicePayment(Request $request) {
    // return $request->all();
    $rules = [
      'date'      => 'required|date',
      'supprefno' => 'required',
      'branchid'  => 'required',
      'paytype'   => 'required',
      'save'      => 'required',
    ];

    $validator = app('validator')->make($request->all(), $rules);

    if ($validator->fails()) 
      return redirect()->back()->withErrors($validator);

    $purchases = $this->purchase->findWhere(['supprefno'=>$request->input('supprefno'), 'date'=>$request->input('date'), 'branchid'=>$request->input('branchid')]);
    if (count($purchases)<=0)
      return redirect()->back()->with('alert-error', 'No records found!')->with('alert-important', '');

    $arr_terms = $purchases->pluck('terms')->toArray();

    $idx = array_search('K', $arr_terms);
    if ($idx !== false) {
      if ($request->input('paytype')>0)
        $update = ['paytype'=>$request->input('paytype'), 'save'=>1];
      else
        $update = ['paytype'=>$request->input('paytype'), 'save'=>0];
    }


    $updated_purchases = DB::table('purchase')
                          ->where(['supprefno'=>$request->input('supprefno'), 'date'=>$request->input('date'), 'branchid'=>$request->input('branchid')])
                          ->update($update);


    if ($updated_purchases>0) {

      // event(new \App\Events\Process\AggregateComponentDaily(c($request->input('fr')), $request->input('branchid'))); // recompute Daily Component
      // event(new \App\Events\Process\AggregateDailyExpense(c($request->input('fr')), $request->input('branchid'))); // recompute Daily Expense

      // event(new \App\Events\Process\AggregateComponentMonthly(c($request->input('fr')), $request->input('branchid'))); // recompute Monthly Component
      // event(new \App\Events\Process\AggregateMonthlyExpense(c($request->input('fr')), $request->input('branchid'))); // recompute Monthly Expense


      // event(new \App\Events\Process\AggregateComponentDaily(c($request->input('to')), $request->input('branchid'))); // recompute Daily Component
      // event(new \App\Events\Process\AggregateDailyExpense(c($request->input('to')), $request->input('branchid'))); // recompute Daily Expense

      // event(new \App\Events\Process\AggregateComponentMonthly(c($request->input('to')), $request->input('branchid'))); // recompute Monthly Component
      // event(new \App\Events\Process\AggregateMonthlyExpense(c($request->input('to')), $request->input('branchid'))); // recompute Monthly Expense

      return redirect('/invoice?branchid='.strtolower($request->input('branchid')).'&date='.$request->input('date').'&supprefno='.$request->input('supprefno'))->with('alert-success', 'Payment status has been updated.');
    } else
      return redirect()->back()->with('alert-error', 'No records found to update.')->with('alert-important', '');


  

  }
}