<?php

namespace App\Console\Commands;

use App\Entity\OrderOffer;
use App\Entity\Users;
use App\Models\CommonModel;
use App\Models\Sms;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * 已通过的报价达到预警条件发送短信给供应商 (Artisan 计划任务)
 * Class OfferWarningSendSms
 * @package App\Console\Commands
 */
class OfferWarningSendSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OfferWarningSendSms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * OfferWarningSendSms constructor.
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
        $e_order_offer = new OrderOffer();
        $sms = new Sms();
        /*预加载ORM对象*/
        $e_order_offer = $e_order_offer->with(['ho_orders' => function ($query)
        {
            $query->where('is_delete', '=', CommonModel::ORDER_NO_DELETE);
        }])->where('order_offer.status', CommonModel::OFFER_PASSED)->where('order_offer.warning_is_sms', CommonModel::OFFER_NO_SMS)->get();

        /*数据过滤*/
        $e_order_offer->each(function ($item) use ($sms)
        {
            if (empty($item->ho_orders))
            {   /*如果是已删除的订单,将报价删除*/
                $item_delete = OrderOffer::find($item->offer_id);
                $item_delete->delete();
            }
            else
            {
                $item->order_info = $item->ho_orders;
            }
            /*判断是否达到预警条件*/
            if ($item->status == CommonModel::OFFER_PASSED && bcsub($item->order_info->platform_receive_time, $item->warning_time) < now()->timestamp)
            {
                //发送短信
                $sms->sendSms(Sms::SMS_SIGNATURE_1, Sms::SUPPLIER_ALLOCATION_CODE, Users::find($item->user_id)->phone);

                //改变发送短信的状态
                OrderOffer::where('offer_id', $item->offer_id)->update(['warning_is_sms' => CommonModel::OFFER_IS_SMS]);

                //测试log
                Log::info('已通过的报价达到预警条件发送短信给供应商  offer ID:' . $item->offer_id);
            }
        });

    }
}
