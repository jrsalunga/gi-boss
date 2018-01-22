<?php namespace App\Console\Commands;

use DB;
use Carbon\Carbon;
use App\Models\Branch;
use Illuminate\Console\Command;
use App\Repositories\DateRange;
use App\Repositories\DailySalesRepository as DSRepo;
use App\Models\DailySales as DS;

class YearlySales extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'ysales {year : YYYY}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Display an inspiring quote';

  /**
   * Execute the console command.
   *
   * @return mixed
   */


  protected $dr;
  protected $ds;

  public function __construct(DateRange $dr, DSRepo $ds) {
    parent::__construct();
    $this->dr = $dr;
    $this->ds = $ds;
  }


  public function handle()
  {
    $year = $this->argument('year');


    $sql = 'date, MONTH(date) AS month, YEAR(date) as year, SUM(sales) as sales';

    $branches = Branch::where('opendate', '<>', '0000-00-00')
                        ->where('closedate', '=', '0000-00-00')
                        ->orderBy('code')
                        ->get();

    $this->dr->fr = Carbon::parse($year.'-01-01');
    $this->dr->to = Carbon::parse($year.'-12-31');

    /*
    
    */

    foreach ($branches as $key => $branch) {

      //$this->comment(($key+1).' '.$branch->id);
      $text = $branch->code.',';
      
      foreach ($this->dr->monthInterval() as $key => $date) {
       // $this->comment(($key+1).' '.$date->format('Y-m-d'));
        //$this->comment($branch->id);

        $fr = $date->startOfMonth()->format('Y-m-d');
        $to = $date->endOfMonth()->format('Y-m-d');

        $ds = DS::select(DB::raw($sql))
          ->where('branchid', $branch->id)
          ->whereBetween('date', [$fr, $to])
          ->groupBy(DB::raw('MONTH(date), YEAR (date)'))
          ->orderBy(DB::raw('YEAR (date), MONTH(date)'))
          ->first();

        if (isset($ds->sales))
          $text = $text.$ds->sales.',';
        else
          $text = $text.',';
        //$this->comment($ds->sales);
      }
      
      $this->comment($text);
    }
  }







}
