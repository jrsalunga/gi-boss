<?php namespace App\Console\Commands\Rerun;

use DB;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Employee;
use App\User;

class UserOrdinal extends Command {

	protected $empCtrl;
	protected $signature = 'rerun:user-ordinal';
  protected $description = 'chnage the punching field for position ordinal';

  


	public function handle() {

		$users = User::orderBy('username')->get();

		foreach ($users as $key => $user) {
			$this->info($user->username.' '.$user->name);

			$employee = Employee::find($user->id);

			if (is_null($employee))
				$this->info('-');
			else
				$this->log('>'.$employee->lastname.' '.$employee->firstname);



			
		

		}
		/*
		$employees = Employee::where('punching', '>', 0)->orderBy('code')->get();

		foreach ($employees as $key => $employee) {
			$this->info($employee->code.' '.$employee->lastname.' '.$employee->firstname);

			$ordinal = array_key_exists($employee->positionid, config('giligans.position')) 
				? config('giligans.position')[$employee->positionid]['ordinal']
				: 99;
			
			Employee::where('id', $employee->id)->update(['punching' => $ordinal]);
		}
		*/



  }





}