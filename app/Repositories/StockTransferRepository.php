<?php namespace App\Repositories;

use DB;
use Exception;
use Carbon\Carbon;
use App\Models\Branch;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Traits\Repository as RepoTrait;

class StockTransferRepository extends BaseRepository implements CacheableInterface
//class StockTransferRepository extends BaseRepository 
{
  use CacheableRepository, RepoTrait;

  protected $order = ['date', 'descriptor'];
  
  public function model() {
    return 'App\\Models\\StockTransfer';
  }


  public function getCos($branchid, Carbon $date, array $expcode) {
    return $this->scopeQuery(function($query) use ($branchid, $date, $expcode) {
      return $query->where('stocktransfer.date', $date->format('Y-m-d'))
                    ->where('stocktransfer.branchid', $branchid)
                    ->whereIn('expense.code', $expcode)
                    ->leftJoin('component', 'component.id', '=', 'stocktransfer.componentid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->select(DB::raw('sum(stocktransfer.tcost) as tcost'));
    });
  }

  public function withRelations() {
    return $this->with([
      'supplier'=>function($query) {
        return $query->select(['code', 'descriptor', 'tin', 'id']);
      }, 
      'toBranch'=>function($query) {
        return $query->select(['code', 'descriptor', 'tin', 'id']);
      },
      'toSupplier'=>function($query) {
        return $query->select(['code', 'descriptor', 'tin', 'id']);
      }
    ]);
  }

  public function branchByDR(Branch $branch, DateRange $dr) {
    return $dss = $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('stocktransfer.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('component', 'component.id', '=', 'stocktransfer.componentid')
                    ->leftJoin('supplier', 'supplier.id', '=', 'stocktransfer.supplierid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->leftJoin('expscat', 'expscat.id', '=', 'expense.expscatid')
                    ->select('stocktransfer.*', 'component.descriptor as component', 'component.uom as uom',
                        'supplier.code as suppliercode', 'supplier.descriptor as supplier',
                        'compcat.code as compcatcode', 'compcat.descriptor as compcat', 
                        'expense.code as expensecode', 'expense.descriptor as expense',
                        'expscat.code as expscatcode', 'expscat.descriptor as expscat')
                    ->orderBy('stocktransfer.date', 'asc')
                    ->orderBy('component.descriptor', 'asc');
    });
  }


  public function getSumCosByDr($branchid, Carbon $fr, Carbon $to, $expcode) {
    return $this->scopeQuery(function($query) use ($branchid, $fr, $to, $expcode) {
      return $query->whereBetween('stocktransfer.date', [$fr->format('Y-m-d'), $to->format('Y-m-d')])
                    ->where('stocktransfer.branchid', $branchid)
                    ->where('expense.code', $expcode)
                    ->leftJoin('component', 'component.id', '=', 'stocktransfer.componentid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->select(DB::raw('sum(stocktransfer.tcost) as tcost'));
    })->all();
  }
	

}