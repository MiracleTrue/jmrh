<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 13:38
 */

namespace App\Models;

use App\Entity\Orders;
use App\Entity\Users;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * 军方相关模型
 * Class Army
 * @package App\Models
 */
class Army extends CommonModel
{
    /*军方发布的订单*/
    const ORDER_TYPE_ARMY = 1;

    /**
     * 获取所有军方订单列表 (已转换:状态文本, 创建时间, 军方接收时间) (如有where 则加入新的sql条件) "分页" | 默认排序:创建时间
     * @param array $where & [['users.identity', '=', '2'],['nick_name', 'like', '%:00%']]
     * @param array $orderBy
     * @return mixed
     */
    public function getOrderList($where = array(), $orderBy = array(['orders.create_time', 'desc']))
    {
        /*初始化*/
        $e_orders = new Orders();
        /*预加载ORM对象*/
        $e_orders = $e_orders->where('orders.is_delete', $this::ORDER_NO_DELETE)->where('orders.type', $this::ORDER_TYPE_ARMY)
            ->where($where)->with('ho_users');
        foreach ($orderBy as $value)
        {
            $e_orders = $e_orders->orderBy($value[0], $value[1]);
        }
        $order_list = $e_orders->paginate($_COOKIE['PaginationSize']);

        /*数据过滤*/
        $order_list->transform(function ($item)
        {
            $item->army_info = clone $item->ho_users;
            $item->status_text = self::orderStatusTransformText($item->status);
            $item->create_time = Carbon::createFromTimestamp($item->create_time)->toDateTimeString();
            $item->army_receive_time = Carbon::createFromTimestamp($item->army_receive_time)->toDateTimeString();
            $item->army_info->identity_text = User::identityTransformText($item->army_info->identity);
            return $item;
        });
        return $order_list;
    }

    /**
     * 获取 军方视角 单个订单信息 (已转换:状态文本, 创建时间, 军方接收时间)
     * @param $id
     * @return mixed
     */
    public function getOrderInfo($id)
    {
        /*初始化*/
        $e_orders = Orders::where('order_id', $id)
            ->where('is_delete', CommonModel::ORDER_NO_DELETE)
            ->where('type', Army::ORDER_TYPE_ARMY)->first() or die();

        /*数据过滤*/
        $e_orders->status_text = self::orderStatusTransformText($e_orders->status);
        $e_orders->create_time = Carbon::createFromTimestamp($e_orders->create_time)->toDateTimeString();
        $e_orders->army_receive_time = Carbon::createFromTimestamp($e_orders->army_receive_time)->toDateTimeString();
        return $e_orders;
    }

    /**
     * 军方发布需求
     * @param $arr
     * @return bool
     */
    public function releaseNeed($arr)
    {
        /*初始化*/
        $user = new User();
        $sms = new Sms();
        $e_orders = new Orders();

        /*添加*/
        $e_orders->type = self::ORDER_TYPE_ARMY;
        $e_orders->status = $this::ORDER_AWAIT_ALLOCATION;
        $e_orders->order_sn = $this->makeOrderSn();
        $e_orders->product_name = !empty($arr['product_name']) ? $arr['product_name'] : '';
        $e_orders->product_price = !empty($arr['product_price']) ? $arr['product_price'] : 0;
        $e_orders->product_number = !empty($arr['product_number']) ? $arr['product_number'] : 1;
        $e_orders->product_unit = !empty($arr['product_unit']) ? $arr['product_unit'] : '';
        $e_orders->platform_receive_time = 0;
        $e_orders->army_receive_time = !empty($arr['army_receive_time']) ? strtotime($arr['army_receive_time']) : 0;/*2017-10-18 08:45:12*/
        $e_orders->create_time = Carbon::now()->timestamp;
        $e_orders->is_delete = $this::ORDER_NO_DELETE;
        $e_orders->army_id = session('ManageUser')->user_id;

        $e_orders->save();

        /*发送短信给所有平台运营员*/
        $platform_users = $user->getPlatformUserList();
        $platform_users_numbers_str = implode(',', $platform_users->pluck('phone')->unique()->all());
        $sms->sendSms(Sms::SMS_SIGNATURE_1, Sms::ARMY_RELEASE_CODE, $platform_users_numbers_str);
        //测试log
        Log::info('平台发布需求,发送短信给所有平台运营员  order ID:' . $e_orders->order_id . ' Phone:' . $platform_users_numbers_str);


        User::userLog($e_orders->product_name . "($e_orders->product_number $e_orders->product_unit)");
        return true;
    }

    /**
     * 军方修改需求
     * @param $arr
     * @return bool
     */
    public function editNeed($arr)
    {
        /*初始化*/
        $e_orders = Orders::where('order_id', $arr['order_id'])
            ->where('is_delete', CommonModel::ORDER_NO_DELETE)
            ->where('type', Army::ORDER_TYPE_ARMY)
            ->where('status', CommonModel::ORDER_AWAIT_ALLOCATION)->first() or die();

        /*修改*/
        $e_orders->product_name = !empty($arr['product_name']) ? $arr['product_name'] : '';
        $e_orders->product_price = !empty($arr['product_price']) ? $arr['product_price'] : 0;
        $e_orders->product_number = !empty($arr['product_number']) ? $arr['product_number'] : 1;
        $e_orders->product_unit = !empty($arr['product_unit']) ? $arr['product_unit'] : '';
        $e_orders->army_receive_time = !empty($arr['army_receive_time']) ? strtotime($arr['army_receive_time']) : 0;/*2017-10-18 08:45:12*/

        $e_orders->save();
        User::userLog($e_orders->product_name . "($e_orders->product_number $e_orders->product_unit)");
        return true;
    }

    /**
     * 军方删除需求 (伪删除)
     * @param $id
     * @return bool
     */
    public function deleteNeed($id)
    {
        /*初始化*/
        $e_orders = Orders::where('order_id', $id)
            ->where('is_delete', CommonModel::ORDER_NO_DELETE)
            ->where('type', Army::ORDER_TYPE_ARMY)
            ->where('status', CommonModel::ORDER_AWAIT_ALLOCATION)->first();

        /*伪删除*/
        $e_orders->is_delete = $this::ORDER_IS_DELETE;

        $e_orders->save();
        User::userLog($e_orders->product_name . "($e_orders->product_number $e_orders->product_unit)");
        return true;
    }

    /**
     * 军方确认收到平台的供货
     * @param $order_id
     * @return bool
     */
    public function armyConfirmReceive($order_id)
    {
        $e_orders = Orders::where('order_id', $order_id)->where('is_delete', CommonModel::ORDER_NO_DELETE)
            ->where('status', CommonModel::ORDER_SEND_ARMY)->first() or die('order missing');

        $e_orders->status = CommonModel::ORDER_SUCCESSFUL;
        $e_orders->save();
        User::userLog($e_orders->product_name . "($e_orders->product_number$e_orders->product_unit) 订单号: " . $e_orders->order_sn);
        return true;
    }

    /**
     * 返回军方视角 订单状态 的文本名称
     * @param $status
     * @return string
     */
    public function orderStatusTransformText($status)
    {
        $text = '';
        switch ($status)
        {
            case $this::ORDER_AWAIT_ALLOCATION:
                $text = '待确认';
                break;
            case $this::ORDER_AGAIN_ALLOCATION:
                $text = '已确认';
                break;
            case $this::ORDER_ALLOCATION_SUPPLIER:
                $text = '已确认';
                break;
            case $this::ORDER_SUPPLIER_SELECTED:
                $text = '已确认';
                break;
            case $this::ORDER_SUPPLIER_SEND:
                $text = '已确认';
                break;
            case $this::ORDER_SUPPLIER_RECEIVE:
                $text = '已确认';
                break;
            case $this::ORDER_ALLOCATION_PLATFORM:
                $text = '已确认';
                break;
            case $this::ORDER_SEND_ARMY:
                $text = '已发货';
                break;
            case $this::ORDER_SUCCESSFUL:
                $text = '已到货(交易完成)';
                break;
        }
        return $text;
    }
}