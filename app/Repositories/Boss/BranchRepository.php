<?php namespace App\Repositories\Boss;

use App\Repositories\Repository;
use Prettus\Repository\Eloquent\BaseRepository;

use Illuminate\Support\Collection;
use Illuminate\Container\Container as App;

use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Criterias\ActiveBranchCriteria as ActiveBranch;
use App\Traits\Repository as RepoTrait;


class BranchRepository extends BaseRepository implements CacheableInterface
//class BranchRepository extends BaseRepository 
{
  use CacheableRepository, RepoTrait;

  protected $order = ['code', 'descriptor'];

  protected $fieldSearchable = [
    'code'=>'like',
    'descriptor'=>'like',
    'company.descriptor'=>'like',
    'sectorid'=>'like',
  ];

	public function __construct() {
      parent::__construct(app());

			//$this->boots();      
  }

  public function ordered($order=null, $asc='asc'){

    if(!is_null($order)) {
      if (is_array($order)) 
        foreach ($order as $key => $field) 
          $this->orderBy($field, $asc);
      else 
        $this->orderBy($field, $asc);
    } else {
      if (property_exists($this, 'order'))
        $this->order($this->order);
    }
    return $this;
  }

  public function boots() {
  	$this->scopeQuery(function($query){
  		return $query->whereNotIn('id', ['971077BCA54611E5955600FF59FBB323', '3C561250F87448E3A2DD0562B24E3639'])
  								->orderBy('code','asc');
		});
  }

  public function boot(){
    $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
  }

	public function model() {
    return 'App\\Models\\Boss\\Branch';
  }



  public function active(){
    return $this->getByCriteria(new ActiveBranch);
    return $this;
  }

  
 




  
  

    




}