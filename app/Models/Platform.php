<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 13:38
 */

namespace App\Models;

use App\Entity\Orders;
use Illuminate\Support\Carbon;

/**
 * 平台相关模型
 * Class Platform
 * @package App\Models
 */
class Platform extends CommonModel
{
    /*平台发布的订单*/
    const ORDER_TYPE_PLATFORM = 2;

    private $errors = array(); /*错误信息*/

    /**
     * 给单个供应商 分配一个订单
     */
    private function allocationOfferToSupplier($order_id,$)
    {

    }


    /**
     * 平台发布需求
     * @param $arr
     * @return bool
     */
    public function releaseNeed($arr)
    {
        /*初始化*/
        $e_orders = new Orders();

        /*添加*/
        $e_orders->type = self::ORDER_TYPE_PLATFORM;
        $e_orders->status = $this::ORDER_AWAIT_ALLOCATION;
        $e_orders->order_sn = $this->makeOrderSn();
        $e_orders->product_name = !empty($arr['product_name']) ? $arr['product_name'] : '';
        $e_orders->product_number = !empty($arr['product_number']) ? $arr['product_number'] : 1;
        $e_orders->product_unit = !empty($arr['product_unit']) ? $arr['product_unit'] : '';
        $e_orders->platform_receive_time = 0;
        $e_orders->army_receive_time = !empty($arr['army_receive_time']) ? strtotime($arr['army_receive_time']) : 0;/*2017-10-18 08:45:12*/
        $e_orders->create_time = Carbon::now()->timestamp;
        $e_orders->is_delete = $this::ORDER_NO_DELETE;

        $e_orders->save();
        User::userLog($e_orders->product_name . "($e_orders->product_number $e_orders->product_unit)");
        return true;
    }








    /***************************************************************************/

    /**
     * 获取军方订单列表 (已转换:状态文本, 创建时间, 军方接收时间) (如有where 则加入新的sql条件) "分页" | 默认排序:创建时间
     * @param array $where & [['users.identity', '=', '2'],['nick_name', 'like', '%:00%']]
     * @param array $orWhere
     * @param array $orderBy
     * @return mixed
     */
    public function getOrderList($where = array(), $orWhere = array(), $orderBy = array(['orders.create_time', 'desc']))
    {
        /*预加载ORM对象*/
        $e_orders = Orders::where('orders.is_delete', $this::ORDER_NO_DELETE)->where('orders.type', $this::ORDER_TYPE_ARMY)
            ->where($where);
        foreach ($orWhere as $value)
        {
            $e_orders->orWhere($value[0], $value[1], $value[2]);
        }
        foreach ($orderBy as $value)
        {
            $e_orders->orderBy($value[0], $value[1]);
        }
        $order_list = $e_orders->paginate($_COOKIE['PaginationSize']);

        /*数据过滤*/
        $order_list->transform(function ($item)
        {
            $item->status_text = self::orderStatusTransformText($item->status);
            $item->create_time = Carbon::createFromTimestamp($item->create_time)->toDateTimeString();
            $item->army_receive_time = Carbon::createFromTimestamp($item->army_receive_time)->toDateTimeString();
            return $item;
        });
        return $order_list;
    }

    /**
     * 获取 军方视角 单个订单信息 (已转换:状态文本, 创建时间, 军方接收时间)
     * @param $id
     * @return mixed
     */
    public
    function getOrderInfo($id)
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
     * 军方修改需求
     * @param $arr
     * @return bool
     */
    public
    function editNeed($arr)
    {
        /*初始化*/
        $e_orders = Orders::where('order_id', $arr['order_id'])
            ->where('is_delete', CommonModel::ORDER_NO_DELETE)
            ->where('type', Army::ORDER_TYPE_ARMY)
            ->where('status', CommonModel::ORDER_AWAIT_ALLOCATION);

        /*修改*/
        $e_orders->product_name = !empty($arr['product_name']) ? $arr['product_name'] : '';
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
    public
    function deleteNeed($id)
    {
        /*初始化*/
        $e_orders = Orders::where('order_id', $id)
            ->where('is_delete', CommonModel::ORDER_NO_DELETE)
            ->where('type', Army::ORDER_TYPE_ARMY)
            ->where('status', CommonModel::ORDER_AWAIT_ALLOCATION);

        /*伪删除*/
        $e_orders->is_delete = $this::ORDER_IS_DELETE;

        $e_orders->save();
        User::userLog($e_orders->product_name . "($e_orders->product_number $e_orders->product_unit)");
        return true;
    }

    /**
     * 返回军方视角 订单状态 的文本名称
     * @param $status
     * @return string
     */
    public
    function orderStatusTransformText($status)
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
                $text = '已到货';
                break;
        }
        return $text;
    }

    /**
     * 返回 模型 发生的错误信息
     * @return mixed
     */
    public
    function messages()
    {
        return $this->errors;
    }
}