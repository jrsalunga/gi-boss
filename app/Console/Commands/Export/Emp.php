<?php namespace App\Console\Commands\Export;

use DB;
use File;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Http\Controllers\EmpController as EmpCtrl;

class Emp extends Command {

	protected $empCtrl;
	protected $signature = 'export:emp {man_no : 000000} {--ext=MAS : file type}';
  protected $description = 'Export employee and generate .EMP file';

  public function __construct(EmpCtrl $empCtrl) {
  	parent::__construct();
  	$this->empCtrl = $empCtrl;
  }


	public function handle() {

		$man_no = $this->argument('man_no');
		$ext = up($this->option('ext'));

		$this->info($man_no);
		$this->info($ext);
		
		$dest = app()->environment()=='local'
				? 'C:\myserver\htdocs\gi-cashier\TEST_FILES_BACKUP\EMPFILE'.DS.$ext
				: config('filesystems.disks.files.production.root').DS.'EMPFILE'.DS.$ext;

		$em = $this->empCtrl->exportByManNo(pad($man_no,6), $ext, $dest);


		$this->info($em);



  }





}