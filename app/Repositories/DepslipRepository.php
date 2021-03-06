<?php namespace App\Repositories;
use DB;
use Exception;
use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Traits\Repository as RepoTrait;
use App\Repositories\Criterias\ByBranch2;


class DepslipRepository extends BaseRepository implements CacheableInterface
//class MenucatRepository extends BaseRepository 
{
  use CacheableRepository, RepoTrait;

  protected $order = ['created_at'];

  public function boot(){
    //$this->pushCriteria(new ByBranch2(request()));
    $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
  }
  
  public function model() {
    return 'App\\Models\\Depslip';
  }

  protected $fieldSearchable = [
    'branch.code',
    'branch.descriptor'=>'like',
    'filename'=>'like',
    'cashier'=>'like',
    'date',
    'type',
    'fileUpload.terminal',
  ];



  private function aggregateDailyLogs(Carbon $fr, Carbon $to, $branchid) {
  	return $this->scopeQuery(function($query) use ($fr, $to, $branchid) {
    	return $query
    						->select(DB::raw('*, count(*) as count'))
    						->whereBetween('date', 
    							[$fr->format('Y-m-d').' 00:00:00', $to->format('Y-m-d').' 23:59:59']
    							)
                ->where('branch_id', $branchid)
    						->groupBy(DB::raw('DAY(date)'))
    						->orderBy('created_at', 'DESC');
    						//->orderBy('filedate', 'DESC');
		})->all();
  }

  public function branchByDR(Carbon $fr, Carbon $to, $branchid) {
    return $this->scopeQuery(function($query) use ($fr, $to, $branchid) {
      return $query
                #->select(DB::raw('*, count(*) as count'))
                ->whereBetween('date', 
                  [$fr->format('Y-m-d').' 00:00:00', $to->format('Y-m-d').' 23:59:59']
                  )
                ->where('branch_id', $branchid)
                #->groupBy(DB::raw('DAY(date)'))
                ->orderBy('created_at', 'DESC');
                //->orderBy('filedate', 'DESC');
    })->all();
  }


	public function monthlyLogs(Carbon $date, $branch) {
  	$arr = [];
  	$fr = $date->firstOfMonth();
  	$to = $date->copy()->lastOfMonth();

  	$data = $this->aggregateDailyLogs($fr, $to, $branch->id);

  	for ($i=0; $i < $date->daysInMonth; $i++) { 

  		$date = $fr->copy()->addDays($i);

  		$filtered = $data->filter(function ($item) use ($date){
        return $item->date->format('Y-m-d') == $date->format('Y-m-d')
          ? $item : null;
    	});

  		$b = $filtered->first();

  		if(!is_null($b)) {
    		$e = file_exists(config('giligans.path.files.'.app()->environment()).'DEPSLP'.DS.$b->date->format('Y').DS.strtoupper($branch->code).DS.$b->date->format('m').DS.$b->filename);
        $path = config('giligans.path.files.'.app()->environment()).'DEPSLP'.DS.$b->date->format('Y').DS.strtoupper($branch->code).DS.$b->date->format('m').DS.$b->filename;
      }
      else {
        $path = '';
        $e = 0;
      }

  		array_push($arr, [ 
      		'date'=>$date,
      		'item'=>$b,
          'exist'=>$e,
      		'path'=>$path]
      );
  	}

    
    return $arr;
  }


}