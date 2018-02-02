<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 13:38
 */

namespace App\Models;

use App\Entity\Orders;
use App\Entity\ShoppingCart;
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
        $e_orders = $e_orders->where('orders.type', $this::ORDER_TYPE_ARMY)
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
            $item->quality_check_text = self::orderQualityCheckTransformText($item->quality_check);
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
            ->where('type', Army::ORDER_TYPE_ARMY)->firstOrFail();

        /*数据过滤*/
        $e_orders->status_text = self::orderStatusTransformText($e_orders->status);
        $e_orders->quality_check_text = self::orderQualityCheckTransformText($e_orders->quality_check);
        $e_orders->create_time = Carbon::createFromTimestamp($e_orders->create_time)->toDateTimeString();
        $e_orders->army_receive_time = Carbon::createFromTimestamp($e_orders->army_receive_time)->toDateTimeString();
        return $e_orders;
    }

    /**
     * 军方发布单个需求
     * @param $arr
     * @return bool
     */
    public function releaseNeed($arr)
    {
        if (!$product = Product::checkProduct($arr['product_name'], $arr['spec_name']))
        {
            $this->errors['code'] = 1;
            $this->errors['messages'] = '产品不存在';
            return false;
        }
        /*初始化*/
        $sms = new Sms();
        $e_orders = new Orders();

        /*添加*/
        $e_orders->type = self::ORDER_TYPE_ARMY;
        $e_orders->status = $this::ORDER_AWAIT_ALLOCATION;
        $e_orders->order_sn = $this->makeOrderSn();
        $e_orders->category_id = $product->category_id;
        $e_orders->product_thumb = $product->product_thumb;
        $e_orders->product_name = $product->product_name;
        $e_orders->spec_name = $product->spec_info->spec_name;
        $e_orders->spec_unit = $product->spec_info->spec_unit;
        $e_orders->product_number = !empty($arr['product_number']) ? $arr['product_number'] : 1;
        $e_orders->army_contact_person = !empty($arr['army_contact_person']) ? $arr['army_contact_person'] : '';
        $e_orders->army_contact_tel = !empty($arr['army_contact_tel']) ? $arr['army_contact_tel'] : '';
        $e_orders->army_note = !empty($arr['army_note']) ? $arr['army_note'] : '';
        $e_orders->platform_allocation_number = 0;
        $e_orders->army_receive_time = !empty($arr['army_receive_time']) ? strtotime($arr['army_receive_time']) : 0;/*2017-10-18 08:45:12*/
        $e_orders->create_time = Carbon::now()->timestamp;
        $e_orders->quality_check = $this::ORDER_NO_QUALITY_CHECK;
        $e_orders->is_delete = $this::ORDER_NO_DELETE;
        $e_orders->army_id = session('ManageUser')->user_id;
        $e_orders->save();

        /*清除购物车该产品*/
        ShoppingCart::where('user_id', session('ManageUser')->user_id)->where('product_name', $e_orders->product_name)->where('spec_name', $e_orders->spec_name)->delete();

        /*发送短信给负责人运营员*/
        if ($phone = $e_orders->hmt_users->first())
        {
            $phone = $phone->phone;
            $sms->sendSms(Sms::SMS_SIGNATURE_1, Sms::ARMY_RELEASE_CODE, $phone, array('date' => now('Asia/Shanghai')->toDateTimeString(), 'order_sn' => $e_orders->order_sn));
            info('短信-军方发布提醒  order ID:' . $e_orders->order_id . ' Phone:' . $phone);
        }
        User::userLog('订单ID:' . $e_orders->order_id . ',订单号:' . $e_orders->order_sn);
        return true;
    }

    /**
     * 军方修改需求
     * @param $arr
     * @return bool
     */
    public function editNeed($arr)
    {
        if (!$product = Product::checkProduct($arr['product_name'], $arr['spec_name']))
        {
            $this->errors['code'] = 1;
            $this->errors['messages'] = '产品不存在';
            return false;
        }
        /*初始化*/
        $e_orders = Orders::where('order_id', $arr['order_id'])
            ->where('type', Army::ORDER_TYPE_ARMY)
            ->where('status', CommonModel::ORDER_AWAIT_ALLOCATION)->firstOrFail();

        /*修改*/
        $e_orders->order_sn = $this->makeOrderSn();
        $e_orders->category_id = $product->category_id;
        $e_orders->product_thumb = $product->product_thumb;
        $e_orders->product_name = $product->product_name;
        $e_orders->spec_name = $product->spec_info->spec_name;
        $e_orders->spec_unit = $product->spec_info->spec_unit;
        $e_orders->product_number = !empty($arr['product_number']) ? $arr['product_number'] : 1;
        $e_orders->army_contact_person = !empty($arr['army_contact_person']) ? $arr['army_contact_person'] : '';
        $e_orders->army_contact_tel = !empty($arr['army_contact_tel']) ? $arr['army_contact_tel'] : '';
        $e_orders->army_note = !empty($arr['army_note']) ? $arr['army_note'] : '';
        $e_orders->army_receive_time = !empty($arr['army_receive_time']) ? strtotime($arr['army_receive_time']) : 0;/*2017-10-18 08:45:12*/

        $e_orders->save();
        User::userLog('订单ID:' . $e_orders->order_id . ',订单号:' . $e_orders->order_sn);
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
            ->where('type', Army::ORDER_TYPE_ARMY)
            ->where('status', CommonModel::ORDER_AWAIT_ALLOCATION)->firstOrFail();

        /*伪删除*/
        $e_orders->is_delete = $this::ORDER_IS_DELETE;

        $e_orders->save();
        User::userLog('订单ID:' . $e_orders->order_id . ',订单号:' . $e_orders->order_sn);
        return true;
    }

    /**
     * 军方确认收到平台的供货
     * @param $order_id
     * @return bool
     */
    public function armyConfirmReceive($order_id)
    {
        $e_orders = Orders::where('order_id', $order_id)->where('status', CommonModel::ORDER_SEND_ARMY)->firstOrFail();

        $e_orders->status = CommonModel::ORDER_SUCCESSFUL;
        $e_orders->save();
        User::userLog('订单ID:' . $e_orders->order_id . ',订单号:' . $e_orders->order_sn);
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
            case $this::ORDER_ALREADY_ALLOCATION:
                $text = '已确认';
                break;
            case $this::ORDER_ALREADY_CONFIRM:
                $text = '已确认';
                break;
            case $this::ORDER_ALREADY_RECEIVE:
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