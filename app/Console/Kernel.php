<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('HandleOverdueOffer')->everyMinute();/*处理已过确认时间的报价,改为已超期 (Artisan 计划任务)*/


//        $schedule->call(function () {
//            $prefix_path = Storage::disk('local')->getAdapter()->getPathPrefix();
//
//            $a = new File($prefix_path . 'thumb/201710/4/4MXHPAO6cwbbtPIVPoYGWoxhImDQlW3tDorS6PPJ.jpeg');
//            $path = Storage::disk('local')->putFileAs('temp', $a, date('H-i-s',time()) . '.jpeg');
//        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
