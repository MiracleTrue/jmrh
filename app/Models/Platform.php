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
use App\Tools\MyHelper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 平台相关模型
 * Class Platform
 * @package App\Models
 */
class Platform extends CommonModel
{
    /*平台发布的订单*/
    const ORDER_TYPE_PLATFORM = 2;

    private $errors = array('code' => 0, 'messages' => 'OK'); /*错误信息*/

    /**
     * 获取所有订单列表 关联军方信息 (已转换:状态文本, 创建时间, 平台接收时间, 军方接收时间) (如有where 则加入新的sql条件) "分页" | 默认排序:创建时间
     * @param array $where & [['users.identity', '=', '2'],['nick_name', 'like', '%:00%']]
     * @param array $orderBy
     * @return mixed
     */
    public function getOrderList($where = array(), $orderBy = array(['orders.create_time', 'desc']))
    {
        /*预加载ORM对象*/
        $e_orders = Orders::where('orders.is_delete', $this::ORDER_NO_DELETE)
            ->where($where)->with('ho_users');
        foreach ($orderBy as $value)
        {
            $e_orders->orderBy($value[0], $value[1]);
        }
        $order_list = $e_orders->paginate($_COOKIE['PaginationSize']);

        /*数据过滤*/
        $order_list->transform(function ($item)
        {
            if (!empty($item->ho_users))
            {
                $item->army_info = clone $item->ho_users;
            }
            else
            {
                $item->army_info = null;
            }
            $item->status_text = self::orderStatusTransformText($item->type, $item->status);
            $item->create_time = Carbon::createFromTimestamp($item->create_time)->toDateTimeString();
            $item->platform_receive_time = $item->platform_receive_time ? Carbon::createFromTimestamp($item->platform_receive_time)->toDateTimeString() : '';
            $item->army_receive_time = $item->army_receive_time ? Carbon::createFromTimestamp($item->army_receive_time)->toDateTimeString() : '';
            return $item;
        });
        return $order_list;
    }

    /**
     * 获取 平台视角 单个订单信息 关联报价信息 关联供应商信息 (已转换:状态文本, 创建时间, 军方接收时间)
     * @param $id
     * @return mixed
     */
    public function getOrderInfo($id)
    {
        /*初始化*/
        $e_orders = Orders::where('order_id', $id)
            ->where('is_delete', CommonModel::ORDER_NO_DELETE)->first() or die('order missing');

        /*数据过滤*/
        $e_orders->offer_info = $e_orders->hm_order_offer()->where('create_time', '>=', $e_orders->create_time)->get();/*关联报价信息*/
        if ($e_orders->offer_info->isNotEmpty())/*关联用户信息*/
        {
            $e_orders->offer_info->transform(function ($item)
            {
                $item->status_text = $this->offerStatusTransformText($item->status);
                $item->user_info = $item->ho_users;
                return $item;
            });
        }
        $e_orders->status_text = self::orderStatusTransformText($e_orders->type, $e_orders->status);
        $e_orders->create_time = Carbon::createFromTimestamp($e_orders->create_time)->toDateTimeString();
        $e_orders->army_receive_time = Carbon::createFromTimestamp($e_orders->army_receive_time)->toDateTimeString();
        $e_orders->platform_receive_time = Carbon::createFromTimestamp($e_orders->platform_receive_time)->toDateTimeString();
        return $e_orders;
    }

    /**
     * 分配一个订单到单个供应商
     * @param int $order_id
     * @param int $user_id
     * @param array $data
     * @return bool
     */
    private function allocationOfferToSupplier($order_id = 0, $user_id = 0, $data = array())
    {
        /*初始化*/
        $e_order_offer = new OrderOffer();
        $sms = new Sms();

        /*添加*/
        $e_order_offer->order_id = !empty($order_id) ? $order_id : die('order_id missing.');
        $e_order_offer->user_id = !empty($user_id) ? $user_id : die('user_id missing.');
        $e_order_offer->status = $this::OFFER_AWAIT_OFFER;
        $e_order_offer->price = 0;
        $e_order_offer->total_price = 0;
        $e_order_offer->create_time = Carbon::now()->timestamp;
        $e_order_offer->confirm_time = MyHelper::is_timestamp($data['confirm_time']) ? $data['confirm_time'] : die('confirm_time missing');
        $e_order_offer->warning_time = !empty($data['warning_time']) ? $data['warning_time'] : 0;
        $e_order_offer->allocation_user_id = session('ManageUser')->user_id;
        if ($e_order_offer->save())
        {
            $sms->sendSms(Sms::SMS_SIGNATURE_1, Sms::SUPPLIER_ALLOCATION_CODE, Users::find($e_order_offer->user_id)->phone);/*发送短信*/
            return true;
        }
        return false;
    }

    /**
     * 分配军方或平台需求到供应商
     * @param $arr
     * @param $supplier_arr
     * @return bool
     */
    public function allocationSupplier($arr, $supplier_arr)
    {
        if (!is_array($supplier_arr) || empty($supplier_arr))
        {
            $this->errors['code'] = 1;
            $this->errors['messages'] = '供应商不能为空';
            return false;
        }
        /*事物*/
        try
        {
            DB::transaction(function () use ($arr, $supplier_arr)
            {
                /*初始化*/
                $e_orders = Orders::where('order_id', $arr['order_id'])->whereIn('type', [Army::ORDER_TYPE_ARMY, Platform::ORDER_TYPE_PLATFORM])->where('is_delete', CommonModel::ORDER_NO_DELETE)
                    ->whereIn('status', [CommonModel::ORDER_AWAIT_ALLOCATION, CommonModel::ORDER_AGAIN_ALLOCATION])->lockForUpdate()->first();
                if ($e_orders == false)
                {
                    throw new \Exception('Transaction Exception');
                }
                /*更新*/
                $e_orders->status = $this::ORDER_ALLOCATION_SUPPLIER;/*已分配*/
                $e_orders->platform_receive_time = !empty($arr['platform_receive_time']) ? strtotime($arr['platform_receive_time']) : 0;/*2017-10-18 08:45:12*/;
                $e_orders->create_time = Carbon::now()->timestamp;/*更新下单时间*/
                $e_orders->save();
                foreach ($supplier_arr as $item)
                {
                    $this->allocationOfferToSupplier($e_orders->order_id, $item, array('confirm_time' => strtotime($arr['confirm_time']), 'warning_time' => $arr['warning_time']));
                }
                User::userLog($e_orders->product_name . "($e_orders->product_number$e_orders->product_unit) 供应商: " . implode(',', Users::whereIn('user_id', $supplier_arr)->get()->pluck('nick_name')->all()));
            });
        } catch (\Exception $e)
        {
            $this->errors['code'] = 2;
            $this->errors['messages'] = '网络繁忙';
            return false;
        }
        return true;
    }

    /**
     * 为订单 选择最终供应商
     * @param $order_id
     * @param $offer_id
     * @return bool
     */
    public function selectedSupplier($order_id, $offer_id)
    {
        /*初始化*/
        $sms = new Sms();
        $e_orders = Orders::where('order_id', $order_id)->where('is_delete', CommonModel::ORDER_NO_DELETE)
            ->whereIn('status', [CommonModel::ORDER_ALLOCATION_SUPPLIER])->first() or die('order missing');
        $e_order_offer = OrderOffer::where('offer_id', $offer_id)->where('order_id', $order_id)
            ->where('status', CommonModel::OFFER_AWAIT_PASS)->where('create_time', '>=', $e_orders->create_time)->first() or die('order_offer missing');

        /*更新*/
        $e_orders->status = $this::ORDER_SUPPLIER_SELECTED;/*订单状态改变*/
        $e_order_offer->status = $this::OFFER_PASSED;/*报价状态改变*/

        /*事物*/
        DB::transaction(function () use ($e_orders, $e_order_offer, $sms)
        {
            /*保存状态*/
            $e_orders->save();
            $e_order_offer->save();
            /*修改未通过的报价状态*/
            OrderOffer::where('order_id', '=', $e_orders->order_id)
                ->where('offer_id', '!=', $e_order_offer->offer_id)
                ->update(['status' => CommonModel::OFFER_NOT_PASS]);
            $supplier = Users::find($e_order_offer->user_id);
            User::userLog($e_orders->product_name . "($e_orders->product_number$e_orders->product_unit) 选择供应商: " . $supplier->nick_name);
            $sms->sendSms(Sms::SMS_SIGNATURE_1, Sms::SELECT_SUPPLIER_CODE, $supplier->phone);/*发送短信*/
        });
        return true;
    }

    /**
     * 发布平台需求
     * @param $arr
     * @param array $supplier_arr & 供应商数组 [28,185,66]
     * @return bool
     */
    public function releaseNeed($arr, $supplier_arr = array())
    {
        if (!is_array($supplier_arr) || empty($supplier_arr))
        {
            $this->errors['code'] = 1;
            $this->errors['messages'] = '供应商不能为空';
            return false;
        }
        /*初始化*/
        $e_orders = new Orders();
        /*添加*/
        $e_orders->type = self::ORDER_TYPE_PLATFORM;
        $e_orders->status = $this::ORDER_ALLOCATION_SUPPLIER;/*已分配*/
        $e_orders->order_sn = $this->makeOrderSn();
        $e_orders->product_name = !empty($arr['product_name']) ? $arr['product_name'] : '';
        $e_orders->product_number = !empty($arr['product_number']) ? $arr['product_number'] : 1;
        $e_orders->product_price = !empty($arr['product_price']) ? $arr['product_price'] : 0;
        $e_orders->product_unit = !empty($arr['product_unit']) ? $arr['product_unit'] : '';
        $e_orders->platform_receive_time = !empty($arr['platform_receive_time']) ? strtotime($arr['platform_receive_time']) : 0;/*2017-10-18 08:45:12*/;
        $e_orders->army_receive_time = 0;
        $e_orders->create_time = Carbon::now()->timestamp;
        $e_orders->is_delete = $this::ORDER_NO_DELETE;
        /*事物*/
        DB::transaction(function () use ($e_orders, $arr, $supplier_arr)
        {
            $e_orders->save();
            foreach ($supplier_arr as $item)
            {
                $this->allocationOfferToSupplier($e_orders->order_id, $item, array('confirm_time' => strtotime($arr['confirm_time']), 0));
            }
            User::userLog($e_orders->product_name . "($e_orders->product_number$e_orders->product_unit) 供应商: " . implode(',', Users::whereIn('user_id', $supplier_arr)->get()->pluck('nick_name')->all()));
        });
        return true;
    }

    /**
     * 平台 库存供应 单个订单
     * @param $order_id
     * @return bool
     */
    public function inventorySupplyNeed($order_id)
    {
        $e_orders = Orders::where('order_id', $order_id)->where('type', Army::ORDER_TYPE_ARMY)->where('is_delete', CommonModel::ORDER_NO_DELETE)
            ->whereIn('status', [CommonModel::ORDER_AWAIT_ALLOCATION, CommonModel::ORDER_AGAIN_ALLOCATION])->first() or die('order missing');

        $e_orders->status = CommonModel::ORDER_ALLOCATION_PLATFORM;
        $e_orders->save();
        User::userLog($e_orders->product_name . "($e_orders->product_number$e_orders->product_unit) 订单号: " . $e_orders->order_sn);
        return true;
    }

    /**
     * 平台确认收到供应商的供货
     * @param $order_id
     * @return bool
     */
    public function supplierConfirmReceive($order_id)
    {
        $e_orders = Orders::where('order_id', $order_id)->where('is_delete', CommonModel::ORDER_NO_DELETE)
            ->whereIn('status', [CommonModel::ORDER_SUPPLIER_SEND])->first() or die('order missing');

        if ($e_orders->type == Platform::ORDER_TYPE_PLATFORM)
        {
            $e_orders->status = CommonModel::ORDER_SUCCESSFUL;
        }
        elseif ($e_orders->type == Army::ORDER_TYPE_ARMY)
        {
            $e_orders->status = CommonModel::ORDER_SUPPLIER_RECEIVE;
        }
        else
        {
            return false;
        }
        $e_orders->save();
        User::userLog($e_orders->product_name . "($e_orders->product_number$e_orders->product_unit) 订单号: " . $e_orders->order_sn);
        return true;
    }

    /**
     * 发货到军方
     * @param $order_id
     * @return bool
     */
    public function platformSendArmy($order_id)
    {
        $e_orders = Orders::where('order_id', $order_id)->where('is_delete', CommonModel::ORDER_NO_DELETE)
            ->whereIn('status', [CommonModel::ORDER_SUPPLIER_RECEIVE, CommonModel::ORDER_ALLOCATION_PLATFORM])->first() or die('order missing');

        $e_orders->status = CommonModel::ORDER_SEND_ARMY;
        $e_orders->save();
        User::userLog($e_orders->product_name . "($e_orders->product_number$e_orders->product_unit) 订单号: " . $e_orders->order_sn);
        return true;
    }

    /**
     * 返回平台视角 订单状态 的文本名称
     * @param $type
     * @param $status
     * @return string
     */
    public function orderStatusTransformText($type, $status)
    {
        $text = '';
        if ($type == Army::ORDER_TYPE_ARMY)
        {
            switch ($status)
            {
                case $this::ORDER_AWAIT_ALLOCATION:
                    $text = '待分配';
                    break;
                case $this::ORDER_AGAIN_ALLOCATION:
                    $text = '重新分配';
                    break;
                case $this::ORDER_ALLOCATION_SUPPLIER:
                    $text = '已分配';
                    break;
                case $this::ORDER_SUPPLIER_SELECTED:
                    $text = '供应商未发货';
                    break;
                case $this::ORDER_SUPPLIER_SEND:
                    $text = '供应商已发货';
                    break;
                case $this::ORDER_SUPPLIER_RECEIVE:
                    $text = '供应商货已到';
                    break;
                case $this::ORDER_ALLOCATION_PLATFORM:
                    $text = '库存供应';
                    break;
                case $this::ORDER_SEND_ARMY:
                    $text = '已发货到军方';
                    break;
                case $this::ORDER_SUCCESSFUL:
                    $text = '军方已收货(交易成功)';
                    break;
            }
        }
        elseif ($type == Platform::ORDER_TYPE_PLATFORM)
        {
            switch ($status)
            {
                case $this::ORDER_AGAIN_ALLOCATION:
                    $text = '重新分配';
                    break;
                case $this::ORDER_ALLOCATION_SUPPLIER:
                    $text = '已分配';
                    break;
                case $this::ORDER_SUPPLIER_SELECTED:
                    $text = '供应商未发货';
                    break;
                case $this::ORDER_SUPPLIER_SEND:
                    $text = '供应商已发货';
                    break;
                case $this::ORDER_SUPPLIER_RECEIVE:
                    $text = '供应商货已到';
                    break;
                case $this::ORDER_SUCCESSFUL:
                    $text = '平台已收货(交易成功)';
                    break;
            }
        }
        return $text;
    }

    /**
     * 返回 模型 发生的错误信息
     * @return mixed
     */
    public function messages()
    {
        return $this->errors;
    }
}