<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 13:38
 */

namespace App\Models;

use App\Entity\OrderLog;
use App\Tools\M3Result;
use Carbon\Carbon;

/**
 * Class CommonModel 基础模型
 * @package App\Models
 */
class CommonModel
{
    protected $errors = array('code' => 0, 'messages' => 'OK'); /*错误信息*/

    /*订单删除状态:   1.删除  0.正常*/
    const ORDER_IS_DELETE = 1;
    const ORDER_NO_DELETE = 0;

    /*订单质检状态:  1.已质检  0.未质检*/
    const ORDER_IS_QUALITY_CHECK = 1;
    const ORDER_NO_QUALITY_CHECK = 0;

    /*订单状态:*/
    const ORDER_AWAIT_ALLOCATION = 0;/*待分配*/
    const ORDER_AGAIN_ALLOCATION = 1;/*重新分配*/
    const ORDER_ALREADY_ALLOCATION = 100;/*已分配*/
    const ORDER_ALREADY_CONFIRM = 110;/*已确认(等待发货)*/
    const ORDER_ALREADY_RECEIVE = 120;/*已收货(供应商已全部到货)*/
    const ORDER_ALLOCATION_PLATFORM = 200;/*库存供应(平台供应全部货物)*/
    const ORDER_SEND_ARMY = 1000;/*已发货到军方*/
    const ORDER_SUCCESSFUL = 9000;/*军方已收货(交易成功) 或 平台已收货(交易成功)*/

    /*报价状态:*/
    const OFFER_OVERDUE = -1;/*已超期*/
    const OFFER_AWAIT_REPLY = 0;/*待回复*/
    const OFFER_AWAIT_CONFIRM = 1;/*待确认*/
    const OFFER_AWAIT_SEND = 2;/*待发货*/
    const OFFER_ALREADY_SEND = 3;/*已发货*/
    const OFFER_ALREADY_RECEIVE = 4;/*已收货*/
    const OFFER_ALREADY_DENY = 10;/*已拒绝*/

    /*报价预警状态*/
    const OFFER_IS_WARNING = 1;/*预警开启*/
    const OFFER_NO_WARNING = 0;/*无预警*/

    /*报价预警是否发送过短信*/
    const OFFER_IS_SMS = 1;/*已发送短信*/
    const OFFER_NO_SMS = 0;/*未发送过短信*/

    /**
     * 生成唯一订单号
     * @return string
     */
    public function makeOrderSn()
    {
        $time = explode(" ", microtime());
        $time = $time[1] . ($time[0] * 1000);
        $time = explode(".", $time);
        $time = isset($time[1]) ? $time[1] : 0;
        $time = date('YmdHis') + $time;

        return $time . str_pad(mt_rand(1, 99999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * 获取单个订单的日志
     * @param $order_id
     * @return mixed
     */
    public function getOrderLog($order_id)
    {
        /*初始化*/
        $e_order_log = OrderLog::where('order_id', $order_id)->orderBy('create_time', 'asc')->get();

        /*数据过滤*/
        $e_order_log->transform(function ($item)
        {
            $item->create_date = Carbon::createFromTimestamp($item->create_time)->toDateTimeString();
            return $item;
        });

        return $e_order_log;
    }

    /**
     * 返回 报价状态 的文本名称
     * @param $offer_status
     * @return string
     */
    public function offerStatusTransformText($offer_status)
    {
        $text = '';
        switch ($offer_status)
        {
            case $this::OFFER_OVERDUE:
                $text = '已超期';
                break;
            case $this::OFFER_AWAIT_REPLY:
                $text = '待回复';
                break;
            case $this::OFFER_AWAIT_CONFIRM:
                $text = '待确认';
                break;
            case $this::OFFER_AWAIT_SEND:
                $text = '待发货';
                break;
            case $this::OFFER_ALREADY_SEND:
                $text = '已发货';
                break;
            case $this::OFFER_ALREADY_RECEIVE:
                $text = '已收货';
                break;
            case $this::OFFER_ALREADY_DENY:
                $text = '已拒绝';
                break;
        }
        return $text;
    }

    /**
     * 返回 订单质检状态 的文本名称
     * @param $status
     * @return string
     */
    public function orderQualityCheckTransformText($status)
    {
        $text = '';
        switch ($status)
        {
            case $this::ORDER_NO_QUALITY_CHECK:
                $text = '未质检';
                break;
            case $this::ORDER_IS_QUALITY_CHECK:
                $text = '合格';
                break;
        }
        return $text;
    }

    /**
     * 根据请求方式,返回不同的"没有"权限的信息
     * @param $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public static function noPrivilegePrompt($request)
    {
        if ($request->method() == 'GET')/*页面*/
        {
            die('没有权限访问');
        }
        elseif ($request->method() == 'POST')/*Json*/
        {
            $m3result = new M3Result();
            $m3result->code = -1;
            $m3result->messages = '没有权限访问';
            die($m3result->toJson());
        }
        else
        {
            die('没有权限访问');
        }
    }

    /**
     * 订单日志生成,请在模型中调用,并写入描述
     * @param $order_id
     * @param string $desc
     */
    public static function orderLog($order_id, $desc = '')
    {
        /*初始化*/
        $order_log = new OrderLog();

        /*入库*/
        $order_log->order_id = $order_id;
        $order_log->log_desc = $desc;
        $order_log->create_time = now()->timestamp;
        $order_log->save();
    }

    /**
     * 返回 模型 发生的错误信息
     * @return mixed
     */
    public function messages()
    {
        return $this->errors;
    }

//    /*订单状态:*/
//    const ORDER_AWAIT_ALLOCATION = 0;/*待分配*/
//    const ORDER_AGAIN_ALLOCATION = 1;/*重新分配*/
//    const ORDER_ALLOCATION_SUPPLIER = 100;/*已分配供应商*/
//    const ORDER_SUPPLIER_SELECTED = 110;/*已选择供应商*/
//    const ORDER_SUPPLIER_SEND = 120;/*供应商已发货*/
//    const ORDER_SUPPLIER_RECEIVE = 130;/*供应商货已到*/
//    const ORDER_ALLOCATION_PLATFORM = 200;/*库存供应*/
//    const ORDER_SEND_ARMY = 1000;/*已发货到军方*/
//    const ORDER_SUCCESSFUL = 9000;/*军方已收货(交易成功) 或 平台已收货(交易成功)*/
//
//    /*报价状态:*/
//    const OFFER_OVERDUE = -1;/*已超期*/
//    const OFFER_AWAIT_OFFER = 0;/*待报价*/
//    const OFFER_AWAIT_PASS = 1;/*等待通过*/
//    const OFFER_NOT_PASS = 2;/*未通过*/
//    const OFFER_PASSED = 3;/*已通过*/
//    const OFFER_SEND = 4;/*已发货*/


    /*单价方式*/
//$calculated_price = floatval(sprintf("%.4f", $price));;/*保留4位小数的单价(舍去法 取4位浮点数)*/
//$calculated_total = bcmul($calculated_price, $e_orders->product_number, 2);/*保留2位小数的总价(舍去法 取2位浮点数)*/

    /*总价方式*/
//$calculated_total = round($total_price, 2);/*保留2位小数的总价(小数第3位四舍五入)*/
//$calculated_price = bcdiv($total_price, $e_orders->product_number, 4);/*保留4位小数的单价(舍去法 取4位浮点数)*/


}