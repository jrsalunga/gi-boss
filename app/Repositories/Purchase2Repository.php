<?php namespace App\Repositories;

use Carbon\Carbon;
use App\Repositories\Repository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Traits\Itemizable;
use App\Models\Branch;
use App\Repositories\DateRange;


//class Purchase2Repository extends BaseRepository implements CacheableInterface
class Purchase2Repository extends BaseRepository 
{
  //use CacheableRepository;
  use Itemizable;

	public function __construct() {
    parent::__construct(app());

  }

	public function model() {
    return 'App\Models\Purchase2';
  }

  

  public function deleteWhere(array $where){
  	return $this->model->where($where)->delete();
  }

  public function branchByDR(Branch $branch, DateRange $dr) {
    return $dss = $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                    ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplierid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->leftJoin('expscat', 'expscat.id', '=', 'expense.expscatid')
                    ->select('purchase.*', 'component.descriptor as component', 'component.uom as uom',
                        'supplier.code as suppliercode', 'supplier.descriptor as supplier',
                        'compcat.code as compcatcode', 'compcat.descriptor as compcat', 
                        'expense.code as expensecode', 'expense.descriptor as expense',
                        'expscat.code as expscatcode', 'expscat.descriptor as expscat')
                    ->orderBy('purchase.date', 'asc')
                    ->orderBy('component.descriptor', 'asc');
  });
  }






  

}