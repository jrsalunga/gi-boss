<?php namespace App\Repositories;
use DB;
use Carbon\Carbon;
use App\Repositories\Repository;
use Prettus\Repository\Eloquent\BaseRepository;

use Illuminate\Support\Collection;
use Illuminate\Container\Container as App;

use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Criteria\RequestCriteria;


class BackupRepository extends BaseRepository implements CacheableInterface
//class BackupRepository extends BaseRepository 
{
  use CacheableRepository;

	public function __construct() {
      parent::__construct(app());
  }

  public function boot(){
    $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
  }

	public function model() {
    return 'App\\Models\\Backup';
  }

  protected $fieldSearchable = [
    'branch.code'=>'like',
    'filename'=>'like',
    'cashier'=>'like',
  ];



  private function aggregateDailyLogs(Carbon $fr, Carbon $to, $branchid) {
    return $this->scopeQuery(function($query) use ($fr, $to, $branchid) {
      return $query
                ->select(DB::raw('*, count(*) as count'))
                ->whereBetween('filedate', 
                  [$fr->format('Y-m-d').' 00:00:00', $to->format('Y-m-d').' 23:59:59']
                  )
                ->where('branchid', $branchid)
                ->groupBy(DB::raw('DAY(filedate)'))
                ->orderBy('uploaddate', 'DESC');
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
        return $item->filedate->format('Y-m-d') == $date->format('Y-m-d')
          ? $item : null;
      });

      $b = $filtered->first();
      if(!is_null($b))
        $e = file_exists(config('filesystems.disks.backup.'.app()->environment().'.root').$branch->code.DS.$b->year.DS.$b->filedate->format('m').DS.$b->filename);
      else
        $e = 0;
      
      array_push($arr, [ 
          'date'=>$date,
          'backup'=>$b,
          'exist'=>$e]
      );
    }

    
    return $arr;
  }

  




  
  

    




}