<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\YearlySales::class,
        \App\Console\Commands\YearlyProductSales::class,
        \App\Console\Commands\Import\Paymast::class,
        \App\Console\Commands\Export\Emp::class,
        \App\Console\Commands\Rerun\Ordinal::class,
        \App\Console\Commands\Rerun\UserOrdinal::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('inspire')->hourly();
        //$schedule->command('queue:work')->cron('* * * * * *');
        //$schedule->command('queue:work --tries=5')->everyMinute();
    }
}
