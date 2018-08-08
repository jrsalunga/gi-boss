<?php namespace App\Repositories;

use App\User;
use App\Models\Dtr;
use App\Models\Employee;
use App\Models\Department;
use App\Repositories\Repository;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Criterias\ByBranchCriteria;

use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Traits\Repository as RepoTrait;


class EmployeeRepository extends BaseRepository implements CacheableInterface
//class EmployeeRepository extends BaseRepository 
{
  //protected $cacheMinutes = 1;

  use CacheableRepository, RepoTrait;

  protected $order = ['lastname', 'firstname', 'middlename'];

  public function __construct() {
      parent::__construct(app());
      /*
      $this->pushCriteria(new ByBranchCriteria(request()))
      ->scopeQuery(function($query){
        return $query->orderBy('lastname')->orderBy('firstname');
      });
      */
  }

  public function boot(){
    $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
  }


  public function model() {
    return 'App\\Models\\Employee';
  }

  protected $fieldSearchable = [
    'branch.code',
    'position.code',
    'department.code',
    'code'=>'like',
    'lastname'=>'like',
    'firstname'=>'like',
    'middlename'=>'like',
  ];

    /**
     * Get all the DTR of all employee of a branch on a certain date
     *
     * @param  User  $user
     * @return Collection
     */
    public function branchByDate(User $user, $date)
    {
        return Dtr::with(['employee'=>function($query){
						        	$query->select('lastname', 'firstname', 'id');
						        }])
        						->select('dtr.*')
      							->leftJoin('employee', function($join){
                      	$join->on('dtr.employeeid', '=', 'employee.id');
                    })
                    ->where('employee.branchid', '=', $user->branchid)
                    ->where('dtr.date', '=', $date)
                    ->orderBy('employee.lastname', 'ASC')
                    ->orderBy('employee.firstname', 'ASC')->get();
    }



  public function byDepartment(Request $request) {

      $department = new Department;
      $d1 = array_flatten($department->whereNotIn('code', ['KIT', 'CAS'])->orderBy('code', 'DESC')->get(['id'])->toArray());

      $depts = [
        ['code'=>'Din', 'name'=>'Dining', 'employees'=>[], 'deptid'=>$d1],
        ['code'=>'Kit', 'name'=>'Kitchen', 'employees'=>[], 'deptid'=>['71B0A2D2674011E596ECDA40B3C0AA12']],
        ['code'=>'Cas', 'name'=>'Cashier', 'employees'=>[], 'deptid'=>['DC60EC42B0B143AFA7D42312DA5D80BF']]
      ];

      for($i=0; $i<= 2; $i++) { 
          $employees = Employee::with('position')
                                  ->select('lastname', 'firstname', 'positionid', 'employee.id')
                                  ->join('position', 'position.id', '=', 'employee.positionid')
                                  ->where('branchid', $request->user()->branchid)
                                  ->whereIn('deptid', $depts[$i]['deptid'])
                          //->orderBy('position.ordinal', 'ASC')
                          ->orderBy('employee.lastname', 'ASC')
                          ->orderBy('employee.firstname', 'ASC')
                          ->get();
          $depts[$i]['employees'] = $employees;

      }
       return  $depts;
  }



  /*
  * @param: array of employeeid
  * function: get all employees from @param aggregate with dept
  *
  */
  public function byDeptFrmEmpIds(array $empids) {
      $department = new Department;
      $d1 = array_flatten($department->whereNotIn('code', ['KIT', 'CAS'])->orderBy('code', 'DESC')->get(['id'])->toArray());

      $depts = [
        ['code'=>'Din', 'name'=>'Dining', 'employees'=>[], 'deptid'=>$d1],
        ['code'=>'Kit', 'name'=>'Kitchen', 'employees'=>[], 'deptid'=>['71B0A2D2674011E596ECDA40B3C0AA12']],
        ['code'=>'Cas', 'name'=>'Cashier', 'employees'=>[], 'deptid'=>['DC60EC42B0B143AFA7D42312DA5D80BF']]
      ];
      
      for($i=0; $i<= 2; $i++) { 
          $employees = Employee::with('position')
                                  ->select('lastname', 'firstname', 'positionid', 'employee.id')
                                  ->join('position', 'position.id', '=', 'employee.positionid')
                                  //->where('branchid', request()->user()->branchid)
                                  ->whereIn('deptid', $depts[$i]['deptid'])
                                  ->whereIn('employee.id', $empids)
                          //->orderBy('position.ordinal', 'ASC')
                          ->orderBy('employee.lastname', 'ASC')
                          ->orderBy('employee.firstname', 'ASC')
                          ->get();
          $depts[$i]['employees'] = $employees;

      }
       return  $depts;
  }


  public function setTable($table) {
    $instance = new $this->model();
    $instance->setTable($table);
    return $instance;
  }

  public function lastEmpByCode() {
    return $this->skipCache()
      ->orderBy('code', 'desc')
      ->findWhere([['code','like', '0%']])
      ->first();
  }

  public function getLatestCode() {
    $le = $this->lastEmpByCode();

    return (!is_null($le) && isset($le->code))
      ? str_pad(($le->code + 1), 6, '0', STR_PAD_LEFT)
      : str_pad('1', 6, '0', STR_PAD_LEFT);
  }

  public function index_data(Request $request) {
    if ($request->has('search')) {

      return $this->scopeQuery(function($query) {
        return $query->where('datestop', '0000-00-00')
                    ->orderBy('lastname')
                    ->orderBy('firstname')
                    ->orderBy('middlename')
                    ->orderBy('code');
      })->paginate($this->getLimit($request));
    }

    return $this->scopeQuery(function($query) {
      return $query->orderBy('code', 'desc');
    })
    ->paginate($this->getLimit($request));
  }

  private function getLimit(Request $request, $limit = 10) {

    if ( $request->has('limit')
    && filter_var($request->input('limit'), FILTER_VALIDATE_INT, ['options'=>['min_range'=>1, 'max_range'=>100]]) ) {
      return $request->input('limit');
    } else {
      return $limit;
    }
  }
  
  


    


    
}