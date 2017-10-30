<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 13:38
 */

namespace App\Models;

use App\Entity\Orders;
use App\Tools\M3Result;

/**
 * Class CommonModel 基础模型
 * @package App\Models
 */
class CommonModel
{
    /*订单删除状态:   1.删除  0.正常*/
    const ORDER_IS_DELETE = 1;
    const ORDER_NO_DELETE = 0;

    /*订单状态:*/
    const ORDER_AWAIT_ALLOCATION = 0;/*待分配*/
    const ORDER_AGAIN_ALLOCATION = 1;/*重新分配*/
    const ORDER_ALLOCATION_SUPPLIER = 100;/*已分配供应商*/
    const ORDER_SUPPLIER_SELECTED = 110;/*已选择供应商*/
    const ORDER_SUPPLIER_SEND = 120;/*供应商已发货*/
    const ORDER_SUPPLIER_RECEIVE = 130;/*供应商货已到*/
    const ORDER_ALLOCATION_PLATFORM = 200;/*库存供应*/
    const ORDER_SEND_ARMY = 1000;/*已发货到军方*/
    const ORDER_SUCCESSFUL = 9000;/*军方已收货(交易成功) 或 平台已收货(交易成功)*/

    /*报价状态:*/
    const OFFER_OVERTIME = -1;/*已超期*/
    const OFFER_AWAIT_OFFER = 0;/*待报价*/
    const OFFER_AWAIT_PASS = 1;/*等待通过*/
    const OFFER_NOT_PASS = 2;/*未通过*/
    const OFFER_PASSED = 3;/*已通过*/
    const OFFER_SEND = 4;/*已发货*/

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
     * 根据请求方式,返回不同的"没有"权限的信息
     * @param $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public static function noPrivilegePrompt($request)
    {
        if($request->method() == 'GET')/*页面*/
        {
            die('没有权限访问.');
        }
        elseif($request->method() == 'POST')/*Json*/
        {

            $m3result = new M3Result();
            $m3result->code     = -1;
            $m3result->messages = '没有权限访问.';
            die($m3result->toJson());
        }
        else
        {
            die('没有权限访问.');
        }
    }
}