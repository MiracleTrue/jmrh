<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

/**
 * 处理已过确认时间的报价,改为已超期 (Artisan 计划任务)
 * Class HandleOverdueOffer
 * @package App\Console\Commands
 */
class HandleOverdueOffer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'HandleOverdueOffer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '处理已过确认时间的报价,改为已超期 (Artisan 计划任务)';

    /**
     * HandleOverdueOffer constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $prefix_path = Storage::disk('local')->getAdapter()->getPathPrefix();

        $a = new File($prefix_path . 'thumb/201710/4/4MXHPAO6cwbbtPIVPoYGWoxhImDQlW3tDorS6PPJ.jpeg');
        $path = Storage::disk('local')->putFileAs('temp', $a, date('H-i-s',time()) . '.jpeg');
    }
}
