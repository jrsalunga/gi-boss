<?php namespace App\Http\Controllers;

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

    $invoice = [];
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
    
    // return $apus;
    // return $invoice;

    return view('invoice.view', compact('invoice', 'apus'));

  }
  


}