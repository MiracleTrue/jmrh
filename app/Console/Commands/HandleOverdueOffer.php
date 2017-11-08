<?php

namespace App\Console\Commands;

use App\Entity\OrderOffer;
use App\Models\CommonModel;
use Illuminate\Console\Command;

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
        $now_time = now()->timestamp;
        OrderOffer::where('status', CommonModel::OFFER_AWAIT_OFFER)->where('confirm_time', '<', $now_time)
            ->update(['status' => CommonModel::OFFER_OVERDUE, 'price' => 0, 'total_price' => 0]);
    }
}
