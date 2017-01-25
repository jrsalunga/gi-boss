<?php namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Repositories\ProductRepository;
use App\Traits\Repository as RepoTrait;

class SalesmtdRepository extends BaseRepository implements CacheableInterface
//class SalesmtdRepository extends BaseRepository 
{
  use CacheableRepository, RepoTrait;
  
  protected $order = ['orddate', 'ordtime', 'recno'];

  public function model() {
    return 'App\\Models\\Salesmtd';
  }





   public function byDateRange(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('salesmtd.orddate', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('product', 'product.id', '=', 'salesmtd.product_id')
                    ->leftJoin('prodcat', 'prodcat.id', '=', 'product.prodcat_id')
                    ->leftJoin('menucat', 'menucat.id', '=', 'product.menucat_id')
                    ->select('salesmtd.*', 'product.code as productcode', 'product.descriptor as product', 'product.unit as uom',
                        'prodcat.code as prodcatcode', 'prodcat.descriptor as prodcat', 
                        'menucat.code as menucatcode', 'menucat.descriptor as menucat');
    })->order($this->order);
  }
  
	

}