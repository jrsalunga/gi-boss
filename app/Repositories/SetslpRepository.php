<?php namespace App\Repositories;
use DB;
use Exception;
use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Traits\Repository as RepoTrait;

class SetslpRepository extends BaseRepository implements CacheableInterface
//class MenucatRepository extends BaseRepository 
{
  use CacheableRepository, RepoTrait;

  protected $order = ['created_at'];

  public function boot(){
    $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
  }
  
  public function model() {
    return 'App\\Models\\Setslp';
  }

  protected $fieldSearchable = [
    'branch.code',
    'branch.descriptor'=>'like',
    'filename'=>'like',
    'cashier'=>'like',
    'date',
    'terminal_id',
    'fileUpload.terminal',
  ];


  private function aggregateDailyLogs(Carbon $fr, Carbon $to, $branchid) {
  	return $this->scopeQuery(function($query) use ($fr, $to, $branchid) {
    	return $query
    						->where('branch_id', $branchid)
                ->where(function ($query) use ($fr, $to){
                  $query->where(function ($query) use ($fr){
                    $query->where('date', '>=', $fr->format('Y-m-d'))
                          ->where('time', '>', '06:00:00');
                  })
                  ->orWhere(function ($query) use ($to) {
                    $query->where('date', '<=', $to->copy()->addDay()->format('Y-m-d'))
                          ->where('time', '<', '05:59:59');
                  });
                })
    						->orderBy('created_at', 'DESC');
    						//->orderBy('filedate', 'DESC');
		})->skipCache()->all();
  }

  public function getByDr(Carbon $fr, Carbon $to, $branchid) {
    return $this->scopeQuery(function($query) use ($fr, $to, $branchid) {
      return $query
                ->where('branch_id', $branchid)
                ->where(function ($query) use ($fr, $to) {
                  $query->where(function ($query) use ($fr, $to) {
                    $query->whereBetween('date', [$fr->format('Y-m-d'), $to->copy()->addDay()->format('Y-m-d')])
                          ->whereBetween('time', ['06:00:00', '23:59:59']);
                  })
                  ->orWhere(function ($query) use ($fr, $to) {
                    $query->whereBetween('date', [$fr->format('Y-m-d'), $to->copy()->addDay()->format('Y-m-d')])
                          ->whereBetween('time', ['00:00:00', '05:59:59']);
                  });
                })
                ->orderBy('created_at', 'DESC');
    })->skipCache()->all();
  }

  public function getByBizdate(Carbon $date) {
    return $this->scopeQuery(function($query) use ($date) {
      return $query
                ->where(function ($query) use ($date){
                  $query->where(function ($query) use ($date){
                    $query->where('date', '=', $date->format('Y-m-d'))
                          ->whereBetween('time', ['06:00:00', '23:59:59']);
                  })
                  ->orWhere(function ($query) use ($date) {
                    $query->where('date', '=', $date->copy()->addDay()->format('Y-m-d'))
                          ->whereBetween('time', ['00:00:00', '05:59:59']);
                  });
                })
                ->orderBy('created_at', 'DESC');
    })->skipCache()->all();
  }

  public function sumByBizdate(Carbon $date) {
    return $this->scopeQuery(function($query) use ($date) {
      return $query
                ->select(DB::raw('sum(amount) as amount, count(id) as count'))
                ->where(function ($query) use ($date){
                  $query->where(function ($query) use ($date){
                    $query->where('date', '=', $date->format('Y-m-d'))
                          ->whereBetween('time', ['06:00:00', '23:59:59']);
                  })
                  ->orWhere(function ($query) use ($date) {
                    $query->where('date', '=', $date->copy()->addDay()->format('Y-m-d'))
                          ->whereBetween('time', ['00:00:00', '05:59:59']);
                  });
                });
    })->skipCache()->first();
  }


	public function monthlyLogs(Carbon $date, $branchid) {
    $arr = [];
    $fr = $date->firstOfMonth();
    $to = $date->copy()->lastOfMonth();

    $data = $this->aggregateDailyLogs($fr, $to, $branchid);

    for ($i=0; $i < $date->daysInMonth; $i++) { 

      $d = $fr->copy()->addDays($i);
    
      $arr[$i]['date'] = $d;
      $arr[$i]['total'] = 0;
    
      $filtered = $data->filter(function ($item) use ($d){
        $s = c($d->format('Y-m-d').' 06:00:00');
        $e = c($d->copy()->addDay()->format('Y-m-d').' 05:59:59');
        $i = c($item->date->format('Y-m-d').' '.$item->time);
        return $i->gte($s) && $i->lte($e)
          ? $item : null;
      });

      $arr[$i]['count'] = count($filtered);

      $arr[$i]['datas'] = $filtered;

      if (count($filtered)>0) 
        foreach ($filtered as $key => $obj) 
          $arr[$i]['total'] += $obj->amount;

  		
  		
  	}

    
    return $arr;
  }


}