<?php namespace App\Http\Controllers\Hr;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\EmployeeRepository as Employee; 
use App\Repositories\EmploymentActivityRepository as EmpActivity; 

class EmploymentActivityController extends Controller
{

  protected $empActivity;
  protected $employee;

  public function __construct(EmpActivity $empActivity, Employee $employee) {
    $this->empActivity = $empActivity;
     $this->employee = $employee;
  }


  public function getIndex(Request $request, $id) {
    
    try {
      $empActivity = $this->empActivity->skipCache()->with(['branch', 'branchto', 'employee.branch_min'])->find($id);
    } catch (\Exception $e) {
      return abort(404, $e->getMessage());
    }
    
    if (is_null($empActivity))
      return abort(404);

    $employee = $this->employee->skipCache()->with(['branch', 'company', 'position'])->find($empActivity->employee_id);

    return view('hr.masterfiles.employee.employment-activity', compact('employee', 'empActivity'));
  }

  public function action(Request $request) {
    // return $request->all();
    if ($request->input('stage')==1)
      return $this->exReqConfirm($request);
    return abort('404');

  }

  private function exReqConfirm(Request $request) {
    //return $request->all();
    $rules = [
      'id'    => 'required|max:32',
      'stage' => 'required',
      'status' => 'required',
    ];

    if ($request->input('status')==3)
      $rules['notes'] = 'required|max:250';

    $this->validate($request, $rules);

    $empActivity = $this->empActivity->skipCache()->with(['branch', 'branchto', 'employee'])->find($request->input('id'));

    if (is_null($empActivity))
      return redirect()->back()->withErrors(['error'=>'Invalid export request.']);


    $msg = '';
    $data = [
      //'stage'   => 3,
      'stage3'  => Carbon::now(),
    ];


    if ($request->input('status')==1) {
      $msg = 'Export request has been confirmed!';
    }

    if ($request->input('status')==3) {
      $data['status']  = 3;
      $msg = 'Export request has been cancelled!';
    }

    try {
      $e = $this->empActivity->update($data, $empActivity->id);
    } catch (\Exception $e) {
      return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }


    $data['brcode']     = $e->branch->code;
    $data['trail']      = $e->branch->code.'-'.$e->branchto->code;
    $data['type']       = $e->type;
    $data['manno']      = $e->employee->code;
    $data['fullname']   = strtoupper($e->employee->firstname).' '.strtoupper($e->employee->lastname);
    $data['notes']      = NULL;
    $data['cashier']    = session('user.fullname');



    event(new \App\Events\EmploymentActivity\ExportRequest($e, $data));

    return redirect('/hr/masterfiles/employee/employment-activity/'.$e->lid().'?stage='.$e->stage.'&status='.$e->status)
              ->with('alert-success', $msg);

  }


}