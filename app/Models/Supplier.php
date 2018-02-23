<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 13:38
 */

namespace App\Models;

use App\Entity\OrderOffer;
use App\Entity\Orders;
use App\Entity\Users;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * 供应商相关模型
 * Class Platform
 * @package App\Models
 */
class Supplier extends CommonModel
{
    /**
     * 获取所有供应商订单列表 (已转换:状态文本, 创建时间, 平台接收时间, 军方接收时间) (如有where 则加入新的sql条件) "分页" | 默认排序:创建时间
     * @param array $where & [['users.identity', '=', '2'],['nick_name', 'like', '%:00%']]
     * @param array $orderBy
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getOfferList($where = array(), $orderBy = array(['order_offer.create_time', 'desc']))
    {
        /*初始化*/
        $e_order_offer = new OrderOffer();

        /*预加载ORM对象*/
        $e_order_offer = $e_order_offer->with('ho_orders', 'ho_users')->where($where);
        foreach ($orderBy as $value)
        {
            $e_order_offer->orderBy($value[0], $value[1]);
        }
        $offer_list = $e_order_offer->paginate($_COOKIE['PaginationSize']);

        /*数据过滤*/
        $offer_list->transform(function ($item)
        {
            $item->order_info = clone $item->ho_orders;
            $item->user_info = clone $item->ho_users;
            /*如果是已删除的订单,将报价删除*/
            if (empty($item->ho_orders))
            {
                $item_delete = OrderOffer::find($item->offer_id);
                $item_delete->delete();
                header("location: " . action('SupplierController@NeedList'));
            }

            $item->status_text = $this->offerStatusTransformText($item->status);
            $item->warning_status = CommonModel::OFFER_NO_WARNING;
            $item->platform_receive_date = Carbon::createFromTimestamp($item->platform_receive_time)->toDateTimeString();
            /*判断是否达到预警条件*/
            if ($item->status == CommonModel::OFFER_AWAIT_SEND && bcsub($item->platform_receive_time, $item->warning_time) < now()->timestamp)
            {
                $item->warning_status = CommonModel::OFFER_IS_WARNING;
            }
            $item->create_time = Carbon::createFromTimestamp($item->create_time)->toDateTimeString();
            $item->confirm_time = Carbon::createFromTimestamp($item->confirm_time)->toDateTimeString();
            /*order_info*/
            $item->order_info->status_text = self::orderStatusTransformText($item->order_info->status);
            $item->order_info->create_date = Carbon::createFromTimestamp($item->order_info->create_time)->toDateTimeString();
            $item->order_info->army_receive_date = $item->order_info->army_receive_time ? Carbon::createFromTimestamp($item->order_info->army_receive_time)->toDateTimeString() : '';
            /*user_info*/
            $item->user_info->identity_text = User::identityTransformText($item->user_info->identity);

            return $item;
        });

        return $offer_list;
    }

    /**
     * 获取 单个报价信息 关联供应商信息 关联订单信息 (已转换:状态文本, 创建时间, 军方接收时间)
     * @param $offer_id
     * @return mixed
     */
    public function getSupplierOfferInfo($offer_id)
    {
        /*初始化*/
        $e_order_offer = OrderOffer::where('offer_id', $offer_id)->firstOrFail();

        $e_order_offer->order_info = $e_order_offer->ho_orders()->firstOrFail();
        $e_order_offer->user_info = $e_order_offer->ho_users()->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)->firstOrFail();
        /*数据过滤*/
        $e_order_offer->total_price = bcmul($e_order_offer->price, $e_order_offer->product_number, 2);
        $e_order_offer->status_text = self::offerStatusTransformText($e_order_offer->status);
        $e_order_offer->create_date = Carbon::createFromTimestamp($e_order_offer->create_time)->toDateTimeString();
        $e_order_offer->confirm_date = Carbon::createFromTimestamp($e_order_offer->confirm_time)->toDateTimeString();
        $e_order_offer->platform_receive_date = Carbon::createFromTimestamp($e_order_offer->platform_receive_time)->toDateTimeString();
        $e_order_offer->order_info->status_text = self::orderStatusTransformText($e_order_offer->order_info->status);
        $e_order_offer->order_info->create_date = Carbon::createFromTimestamp($e_order_offer->order_info->create_time)->toDateTimeString();
        $e_order_offer->order_info->army_receive_date = Carbon::createFromTimestamp($e_order_offer->order_info->army_receive_time)->toDateTimeString();

        return $e_order_offer;
    }

    /**
     * 单个供应商同意一份报价
     * @param $supplier_id
     * @param $offer_id
     * @return bool
     */
    public function supplierSubmitOffer($supplier_id, $offer_id)
    {
        /*初始化*/
        $sms = new Sms();
        $platform = new Platform();
        $e_order_offer = OrderOffer::where('offer_id', $offer_id)->where('user_id', $supplier_id)->where('status', CommonModel::OFFER_AWAIT_REPLY)->firstOrFail();
        $e_orders = $e_order_offer->ho_orders()->firstOrFail();
        $e_users = $e_order_offer->ho_users()->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)->firstOrFail();

        try
        {
            DB::transaction(function () use ($e_order_offer, $e_orders, $e_users, $sms, $platform)
            {
                /*更新*/
                $e_order_offer->status = CommonModel::OFFER_AWAIT_CONFIRM;
                $e_order_offer->save();

                $this::orderLog($e_orders->order_id, $e_users->nick_name . ' 需供货量:' . $e_order_offer->product_number . ' (同意供货)');
                User::userLog('订单ID:' . $e_orders->order_id . ',订单号:' . $e_orders->order_sn);

                $order_info = $platform->getOrderInfo($e_orders->order_id);
                $confirm_offer = $order_info->offer_info->filter(function ($value)
                {
                    return $value->status == CommonModel::OFFER_AWAIT_CONFIRM;
                });

                /*查询该订单下的offer  如果验证供货数量不正确将订单状态设置为 "重新分配"*/
                if(bcadd($confirm_offer->sum('product_number'), $order_info->platform_allocation_number, 2) != $order_info->product_number)
                {
                    Orders::where('order_id', $e_order_offer->order_id)->update(['status' => CommonModel::ORDER_AGAIN_ALLOCATION]);
                }

                /*发送短信*/
                $phone = Users::find($e_order_offer->allocation_user_id)->phone;
                $sms->sendSms(Sms::SMS_SIGNATURE_1, Sms::SUPPLIER_SUBMIT_OFFER_CODE, $phone,
                    array('supplier_name' => $e_users->nick_name, 'order_sn' => $e_orders->order_sn));
                info('短信-供货商同意供货  order ID:' . $e_orders->order_id . ' Phone:' . $phone);
            });
        } catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    /**
     * 单个供应商拒绝一份报价
     * @param $supplier_id
     * @param $offer_id
     * @param string $deny_reason
     * @return bool
     */
    public function supplierDenyOffer($supplier_id, $offer_id, $deny_reason = '')
    {
        /*初始化*/
        $sms = new Sms();
        $e_order_offer = OrderOffer::where('offer_id', $offer_id)->where('user_id', $supplier_id)->where('status', CommonModel::OFFER_AWAIT_REPLY)->firstOrFail();
        $e_orders = $e_order_offer->ho_orders()->firstOrFail();
        $e_users = $e_order_offer->ho_users()->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)->firstOrFail();

        try
        {
            DB::transaction(function () use ($e_order_offer, $e_orders, $e_users, $deny_reason, $sms)
            {
                /*更新*/
                $e_order_offer->status = CommonModel::OFFER_ALREADY_DENY;
                $e_order_offer->deny_reason = !empty($deny_reason) ? $deny_reason : '';
                $e_order_offer->save();

                $this::orderLog($e_orders->order_id, $e_users->nick_name . ' 需供货量:' . $e_order_offer->product_number . ' (拒绝供货)' . '  原因:' . $deny_reason);
                User::userLog('订单ID:' . $e_orders->order_id . ',订单号:' . $e_orders->order_sn);

                /*发送短信*/
                $phone = Users::find($e_order_offer->allocation_user_id)->phone;
                $sms->sendSms(Sms::SMS_SIGNATURE_1, Sms::SUPPLIER_DENY_OFFER_CODE, $phone,
                    array('supplier_name' => $e_users->nick_name, 'order_sn' => $e_orders->order_sn));
                info('短信-供货商拒绝供货  order ID:' . $e_orders->order_id . ' Phone:' . $phone);

                /*查询该订单下的offer  如果没有"待回复"的报价将订单状态设置为 "重新分配"*/
                $count_order_offer = OrderOffer::where('order_id', $e_order_offer->order_id)->where('status', CommonModel::OFFER_AWAIT_REPLY)->count();
                if ($count_order_offer === 0)
                {
                    /*将order设置为"重新分配"*/
                    Orders::where('order_id', $e_order_offer->order_id)->update(['status' => CommonModel::ORDER_AGAIN_ALLOCATION]);
                }
            });
        } catch (\Exception $e)
        {
            return false;
        }
        return true;
    }

    /**
     * 单个供应商 配货处理
     * @param $supplier_id
     * @param $offer_id
     * @return bool
     */
    public function supplierSendProduct($supplier_id, $offer_id)
    {
        /*初始化*/
        $sms = new Sms();
        $e_order_offer = OrderOffer::where('offer_id', $offer_id)->where('user_id', $supplier_id)->where('status', CommonModel::OFFER_AWAIT_SEND)->firstOrFail();
        $e_orders = $e_order_offer->ho_orders()->firstOrFail();
        $e_users = $e_order_offer->ho_users()->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)->firstOrFail();

        /*更新*/
        $e_order_offer->status = CommonModel::OFFER_ALREADY_SEND;
        $e_order_offer->save();

        $this::orderLog($e_orders->order_id, $e_users->nick_name . ' 需供货量:' . $e_order_offer->product_number . ' (已发货)');
        User::userLog('订单ID:' . $e_orders->order_id . ',订单号:' . $e_orders->order_sn);

        /*发送短信*/
        $phone = Users::find($e_order_offer->allocation_user_id)->phone;
        $sms->sendSms(Sms::SMS_SIGNATURE_1, Sms::SUPPLIER_SEND_CODE, $phone,
            array('supplier_name' => $e_users->nick_name, 'order_sn' => $e_orders->order_sn));
        info('短信-供货商已发货  order ID:' . $e_orders->order_id . ' Phone:' . $phone);

        return true;
    }

    /**
     * 返回供应商视角 订单状态 的文本名称
     * @param $status
     * @return string
     */
    public function orderStatusTransformText($status)
    {
        $text = '';
        switch ($status)
        {
            case $this::ORDER_AGAIN_ALLOCATION:
                $text = '待确认';
                break;
            case $this::ORDER_ALREADY_ALLOCATION:
                $text = '待确认';
                break;
            case $this::ORDER_ALREADY_CONFIRM:
                $text = '已确认';
                break;
            case $this::ORDER_ALREADY_RECEIVE:
                $text = '已收货(交易成功)';
                break;
            case $this::ORDER_SEND_ARMY:
                $text = '已收货(交易成功)';
                break;
            case $this::ORDER_SUCCESSFUL:
                $text = '已收货(交易成功)';
                break;
        }
        return $text;
    }

}