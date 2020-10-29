<?php namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\ChargesRepository as Charges;
use App\Repositories\BranchRepository as Branch;


class ChargesController extends Controller
{

	protected $dr;
	protected $charges;
  protected $branch;

	public function __construct(DateRange $dr, Charges $charges, Branch $branch) {
    $this->dr = $dr;
    $this->charges = $charges;
    $this->branch = $branch;
	}

  private function activeBranch(){
    return array_sort($this->branch->skipCache()->active(['code', 'descriptor', 'id']), 
      function ($value) {
        return $value['code'];
    });
  }


  public function getSaletype(Request $request) {

    $branch = NULL;
    $data = [];
    $stat = [];
    $branches = $this->activeBranch();

    $brid = collect($branches)->pluck('id')->toArray();

    if (is_uuid($request->input('branchid')) && in_array(strtoupper($request->input('branchid')), $brid)) {

      $branch = $this->branch->find($request->input('branchid'));
      $charges = $this->charges->skipCache()->getSaletypeBranchidByDR($branch->id, $this->dr)->all();
      $keys = $charges->groupBy('saletype')->keys();


      $valid = 0;
      foreach ($this->dr->dateInterval2() as $key => $date) {
        $data[$key]['date'] = $date;
        $data[$key]['tot_sales'] = 0;
        $data[$key]['tot_valid'] = 0;
        

        foreach ($keys as $k => $val) {

          $filtered = $charges->filter(function ($item) use ($date, $val) {
            if ($item->orddate == $date->format('Y-m-d') && $item->saletype == $val) 
              return $item;
          })->first();

          if (is_null($filtered)) {
            $data[$key]['data'][$val] = NULL;
          } else {
            $data[$key]['data'][$val] = $filtered;
            $data[$key]['tot_sales'] += $filtered->total;


            if (array_key_exists($val, $stat)) {
              $stat[$val]['sales'] += $filtered->total;
              $stat[$val]['customer'] += $filtered->customer;
              $stat[$val]['txn'] += $filtered->txn;
             
              if ($data[$key]['tot_sales']>0) {
                $stat[$val]['ave_sales'] = $stat[$val]['sales']/($valid+1);
                $stat[$val]['ave_customer'] = $stat[$val]['customer']/($valid+1);
                $stat[$val]['ave_txn'] = $stat[$val]['txn']/($valid+1);
              }
            
            } else {
              $stat[$val]['sales'] = $filtered->total;
              $stat[$val]['customer'] = $filtered->customer;
              $stat[$val]['txn'] = $filtered->txn;
             
              if ($data[$key]['tot_sales']>0) {
                $stat[$val]['ave_sales'] = $stat[$val]['sales']/($valid+1);
                $stat[$val]['ave_customer'] = $stat[$val]['customer']/($valid+1);
                $stat[$val]['ave_txn'] = $stat[$val]['txn']/($valid+1);
              }
            }
          }
        }

        foreach ($keys as $k => $val) {
           $filtered = $charges->filter(function ($item) use ($date, $val) {
            if ($item->orddate == $date->format('Y-m-d') && $item->saletype == $val) 
              return $item;
          })->first();

          if (!is_null($filtered))
            $filtered->ave_sales = number_format(($filtered->total/$data[$key]['tot_sales'])*100,2);            
        }

        
        if ($data[$key]['tot_sales']>0) {
          $valid++;
          $data[$key]['tot_valid'] = $valid;
        }
        
      }
    }

    // return $data;

    return $this->setViewWithDR(view('report.saletype')
                ->with('datas', $data)
                ->with('stats', $stat)
                ->with('branches', $branches)
                ->with('branch', $branch));
  }







  private function setViewWithDR($view){
    $response = new Response($view->with('dr', $this->dr));
    $response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
    $response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
    $response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
    return $response;
  }

  
}