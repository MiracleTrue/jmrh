<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 13:38
 */

namespace App\Models;

use App\Entity\OrderOffer;
use Illuminate\Support\Carbon;

/**
 * 供应商相关模型
 * Class Platform
 * @package App\Models
 */
class Supplier extends CommonModel
{
    private $errors = array(); /*错误信息*/

    /**
     * 获取单个供应商订单列表 (已转换:状态文本, 创建时间, 平台接收时间, 军方接收时间) (如有where 则加入新的sql条件) "分页" | 默认排序:创建时间
     * @param $supplier_id & 供应商id
     * @param array $where & [['users.identity', '=', '2'],['nick_name', 'like', '%:00%']]
     * @param array $orWhere
     * @param array $orderBy
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getOfferList($supplier_id, $where = array(), $orWhere = array(), $orderBy = array(['order_offer.create_time', 'desc']))
    {
        /*初始化*/
        $e_order_offer = new OrderOffer();

        /*预加载ORM对象*/
        $e_order_offer = $e_order_offer->with(['ho_orders' => function ($query)
        {
            $query->where('is_delete', '=', CommonModel::ORDER_NO_DELETE);
        }])->where('order_offer.user_id', $supplier_id)->where($where);

        foreach ($orWhere as $value)
        {
            $e_order_offer->orWhere($value[0], $value[1], $value[2]);
        }
        foreach ($orderBy as $value)
        {
            $e_order_offer->orderBy($value[0], $value[1]);
        }
        $offer_list = $e_order_offer->paginate($_COOKIE['PaginationSize']);

        /*数据过滤*/
        $offer_list->transform(function ($item)
        {
            if (empty($item->ho_orders))
            {   /*如果是已删除的订单,将报价删除*/
                $item_delete = OrderOffer::find($item->offer_id);
                $item_delete->delete();
                header("location: " . action('SupplierController@NeedList'));
            }
            else
            {
                $item->order_info = $item->ho_orders;
            }
            $item->status_text = $this->offerStatusTransformText($item->status);
            $item->create_time = Carbon::createFromTimestamp($item->create_time)->toDateTimeString();
            $item->confirm_time = Carbon::createFromTimestamp($item->confirm_time)->toDateTimeString();
            $item->order_info->status_text = self::orderStatusTransformText($item->order_info->status);
            $item->order_info->create_time = Carbon::createFromTimestamp($item->order_info->create_time)->toDateTimeString();
            $item->order_info->platform_receive_time = Carbon::createFromTimestamp($item->order_info->platform_receive_time)->toDateTimeString();
            $item->order_info->army_receive_time = Carbon::createFromTimestamp($item->order_info->army_receive_time)->toDateTimeString();
            return $item;
        });
        return $offer_list;
    }

    /**
     * 获取 单个供应商 单个报价信息 关联供应商信息 关联订单信息  (已转换:状态文本, 创建时间, 军方接收时间)
     * @param $supplier_id
     * @param $offer_id
     * @return mixed
     */
    public function getSupplierOfferInfo($supplier_id, $offer_id)
    {
        /*初始化*/
        $e_order_offer = OrderOffer::where('offer_id', $offer_id)->where('user_id', $supplier_id)->first() or die('order_offer missing');

        $e_order_offer->order_info = $e_order_offer->ho_orders()->where('is_delete', CommonModel::ORDER_NO_DELETE)->first() or die('order missing');
        $e_order_offer->user_info = $e_order_offer->ho_users()->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)->first() or die('user missing');
        /*数据过滤*/
        $e_order_offer->status_text = self::offerStatusTransformText($e_order_offer->status);
        $e_order_offer->create_time = Carbon::createFromTimestamp($e_order_offer->create_time)->toDateTimeString();
        $e_order_offer->confirm_time = Carbon::createFromTimestamp($e_order_offer->confirm_time)->toDateTimeString();
        $e_order_offer->order_info->status_text = self::orderStatusTransformText($e_order_offer->order_info->status);
        $e_order_offer->order_info->create_time = Carbon::createFromTimestamp($e_order_offer->order_info->create_time)->toDateTimeString();
        $e_order_offer->order_info->platform_receive_time = Carbon::createFromTimestamp($e_order_offer->order_info->platform_receive_time)->toDateTimeString();
        $e_order_offer->order_info->army_receive_time = Carbon::createFromTimestamp($e_order_offer->order_info->army_receive_time)->toDateTimeString();

        return $e_order_offer;
    }

    /**
     * 单个供应商提交一份报价
     * @param $supplier_id
     * @param $offer_id
     * @param $total_price
     * @return bool
     */
    public function supplierSubmitOffer($supplier_id, $offer_id, $total_price)
    {
        /*初始化*/
        $e_order_offer = OrderOffer::where('offer_id', $offer_id)->where('user_id', $supplier_id)->where('status', CommonModel::OFFER_AWAIT_OFFER)->first() or die('order_offer missing');
        $e_orders = $e_order_offer->ho_orders()->where('is_delete', CommonModel::ORDER_NO_DELETE)->first() or die('order missing');
        $e_users = $e_order_offer->ho_users()->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)->first() or die('user missing');


        $calculated_total = round($total_price, 2);/*保留2位小数的总价(小数第3位四舍五入)*/
        $calculated_price = bcdiv($total_price, $e_orders->product_number, 4);/*保留4位小数的单价(舍去法 取4位浮点数)*/

        /*更新*/
        $e_order_offer->total_price = $calculated_total;
        $e_order_offer->price = $calculated_price;
        $e_order_offer->status = CommonModel::OFFER_AWAIT_PASS;
        $e_order_offer->save();
        User::userLog('订单:' . "($e_orders->order_sn $e_orders->product_name $e_orders->product_number$e_orders->product_unit) 报价: " . $e_users->nick_name . "   $calculated_price 元");
        return true;
    }

    /**
     * 单个供应商 配货处理
     * @param $supplier_id
     * @param $offer_id
     * @return bool
     */
    public function supplierSendGoods($supplier_id, $offer_id)
    {
        /*初始化*/
        $e_order_offer = OrderOffer::where('offer_id', $offer_id)->where('user_id', $supplier_id)->where('status', CommonModel::OFFER_PASSED)->first() or die('order_offer missing');
        $e_orders = $e_order_offer->ho_orders()->where('is_delete', CommonModel::ORDER_NO_DELETE)->first() or die('order missing');
        $e_users = $e_order_offer->ho_users()->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)->first() or die('user missing');

        /*更新*/
        $e_order_offer->status = CommonModel::OFFER_SEND;
        $e_order_offer->save();
        User::userLog('订单:' . "($e_orders->order_sn $e_orders->product_name $e_orders->product_number$e_orders->product_unit) " . "配货");
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
            case $this::ORDER_ALLOCATION_SUPPLIER:
                $text = '已分配供应商';
                break;
            case $this::ORDER_SUPPLIER_SELECTED:
                $text = '已选择供应商';
                break;
            case $this::ORDER_SUPPLIER_SEND:
                $text = '供应商已发货';
                break;
            case $this::ORDER_SUPPLIER_RECEIVE:
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

    /**
     * 返回 模型 发生的错误信息
     * @return mixed
     */
    public function messages()
    {
        return $this->errors;
    }
}