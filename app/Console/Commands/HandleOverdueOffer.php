<?php

namespace App\Console\Commands;

use App\Entity\OrderOffer;
use App\Entity\Orders;
use App\Entity\Users;
use App\Models\CommonModel;
use App\Models\Sms;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 处理已过确认时间的报价,改为已超期,订单状态改为重新分配 (Artisan 计划任务)
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
    protected $description = '处理已过确认时间的报价,改为已超期,订单状态改为重新分配 (Artisan 计划任务)';

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
        /*初始化*/
        $sms = new Sms();
        $now_time = now()->timestamp;
        static $o_id = 0;
        //查询出所有 "待报价" 的offer 并且已经再过期时间的
        $e_order_offer = OrderOffer::where('status', CommonModel::OFFER_AWAIT_OFFER)->where('confirm_time', '<', $now_time)->get();

        //循环每个offer
        $e_order_offer->each(function ($item) use (&$o_id, $sms)
        {
            /*保证每个order只处理一次*/
            if ($o_id !== $item->order_id)
            {
                $o_id = $item->order_id;
                $e_orders = Orders::where('order_id', $item->order_id)->where('is_delete', CommonModel::ORDER_NO_DELETE)->first();
                $e_orders->offer_info = $e_orders->hm_order_offer;

                //判断order下的所有offer是否是"待报价 或 已过期" ture为条件成立
                if ($e_orders->offer_info->whereNotIn('status', [CommonModel::OFFER_AWAIT_OFFER, CommonModel::OFFER_OVERDUE])->isEmpty())
                {
                    //条件成立 将order设置为"重新分配" 将offer设置为"已过期"
                    DB::transaction(function () use ($e_orders)
                    {
                        Orders::where('order_id', $e_orders->order_id)->update(['status' => CommonModel::ORDER_AGAIN_ALLOCATION, 'platform_receive_time' => 0]);//将order设置为"重新分配"
                        OrderOffer::where('order_id', $e_orders->order_id)->update(['status' => CommonModel::OFFER_OVERDUE]);//将offer设置为"已过期"
                    });

                    /*发送短信*/
                    $sms_user = Users::find($e_orders->offer_info->first()->allocation_user_id);
                    $sms->sendSms(Sms::SMS_SIGNATURE_1, Sms::AGAIN_ALLOCATION_CODE, $sms_user->phone);

                    //测试log
                    Log::info('(Artisan 计划任务) 处理已过确认时间的报价,改为已超期,订单状态改为重新分配 order ID:' . $item->order_id . '  发送短信给分配人:' . $sms_user->phone);

                }
                else
                {
                    //条件不成立 order的状态不变 将"待报价"的offer设置为"已过期"
                    OrderOffer::where('order_id', $e_orders->order_id)->where('status', CommonModel::OFFER_AWAIT_OFFER)->update(['status' => CommonModel::OFFER_OVERDUE]);

                    //测试log
                    Log::info('(Artisan 计划任务) 处理已过确认时间的报价,改为已超期 order ID:' . $item->order_id);
                }
            }
        });


    }
}
