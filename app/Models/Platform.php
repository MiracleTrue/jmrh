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
use App\Entity\ShoppingCart;
use App\Entity\SupplierPrice;
use App\Entity\Users;
use App\Exceptions\NetworkBusyException;
use App\Exceptions\OutRepertoryException;
use App\Exceptions\SupplierPriceNotFindException;
use App\Tools\MyHelper;
use GuzzleHttp\Client;
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

    /**
     * 仓库Api 查询产品库存数量
     * @param $product_name
     * @param $spec_name
     * @return \Illuminate\Support\Collection
     */
    public function getRepertory($product_name, $spec_name)
    {
        $client = new Client();
        $response = $client->request('GET', 'http://47.97.179.80/service_sel.php',
            [
                'query' =>
                    [
                        'p_name' => $product_name,
                        'p_spe' => $spec_name,
                    ]
            ]
        );
        $json = json_decode($response->getBody()->getContents(), true);
        if ($response->getStatusCode() == 200 && isset($json['code']) && $json['code'] == 0)
        {
            $collect = collect(array(
                'product_name' => $product_name,
                'spec_name' => $spec_name,
                'number' => $json['data']
            ));
        }
        else
        {
            $collect = collect(array(
                'product_name' => $product_name,
                'spec_name' => $spec_name,
                'number' => 0
            ));
        }
        return $collect;
    }

    /**
     * 仓库Api 出库产品 (用于库存供应)
     * @param $product_name
     * @param $spec_name
     * @param $number
     * @return bool
     */
    public function outRepertory($product_name, $spec_name, $number)
    {
        $client = new Client();
        $response = $client->request('GET', 'http://47.97.179.80/service_del.php',
            [
                'query' =>
                    [
                        'p_name' => $product_name,
                        'p_spe' => $spec_name,
                        'p_num' => $number
                    ]
            ]
        );
        $json = json_decode($response->getBody()->getContents(), true);
        if ($response->getStatusCode() == 200 && isset($json['code']) && $json['code'] == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 获取统计列表 关联军方信息 关联分类负责人 (已转换:状态文本, 创建时间, 平台接收时间, 军方接收时间) (如有where 则加入新的sql条件) "分页" | 默认排序:创建时间
     * @param array $where & [['users.identity', '=', '2'],['nick_name', 'like', '%:00%']]
     * @param array $orderBy
     * @param bool $is_paginate & 是否需要分页
     * @return mixed
     */
    public function getStatistics($where = array(), $orderBy = array(['order_offer.create_time', 'desc']), $is_paginate = true)
    {
        /*预加载ORM对象*/
        $e_order_offer = OrderOffer::where($where)->where('status', $this::OFFER_ALREADY_RECEIVE)->with('ho_users', 'ho_orders');
        /*排序规则*/
        foreach ($orderBy as $value)
        {
            $e_order_offer->orderBy($value[0], $value[1]);
        }
        /*是否需要分页*/
        if ($is_paginate === true)
        {
            $offer_list = $e_order_offer->paginate($_COOKIE['PaginationSize']);
        }
        else
        {
            $offer_list = $e_order_offer->get();
        }

        /*数据过滤*/
        $offer_list->transform(function ($item)
        {
            $item->user_info = $item->ho_users;
            $item->order_info = $item->ho_orders;
            $item->create_date = Carbon::createFromTimestamp($item->create_time)->toDateTimeString();
            $item->total_price = bcmul($item->price, $item->product_number, 2);
            unset($item->ho_users);
            unset($item->ho_orders);
            return $item;
        });
        return $offer_list;
    }

    /**
     * 获取所有订单列表 关联军方信息 关联分类负责人 (已转换:状态文本, 创建时间, 平台接收时间, 军方接收时间) (如有where 则加入新的sql条件) "分页" | 默认排序:创建时间
     * @param array $where & [['users.identity', '=', '2'],['nick_name', 'like', '%:00%']]
     * @param array $orderBy
     * @param bool $is_paginate & 是否需要分页
     * @return mixed
     */
    public function getOrderList($where = array(), $orderBy = array(['orders.create_time', 'desc']), $is_paginate = true)
    {
        /*预加载ORM对象*/
        $e_orders = Orders::where($where)->with('ho_users');
        foreach ($orderBy as $value)
        {
            $e_orders->orderBy($value[0], $value[1]);
        }
        /*是否需要分页*/
        if ($is_paginate === true)
        {
            $order_list = $e_orders->paginate($_COOKIE['PaginationSize']);
        }
        else
        {
            $order_list = $e_orders->get();
        }
        /*数据过滤*/
        $order_list->transform(function ($item)
        {
            /*分类负责人*/
            if (!empty($item->category_id))
            {
                $item->manage_user = $item->hmt_users->first();
            }
            else
            {
                $item->manage_user = null;
            }
            /*军方信息*/
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
        $e_orders = Orders::where('order_id', $id)->firstOrFail();

        /*数据过滤*/
        $e_orders->offer_info = $e_orders->hm_order_offer;/*关联报价信息*/
        if ($e_orders->offer_info->isNotEmpty())/*关联用户信息*/
        {
            $e_orders->offer_info->transform(function ($item)
            {
                $item->status_text = $this->offerStatusTransformText($item->status);
                $item->create_date = Carbon::createFromTimestamp($item->create_time)->toDateTimeString();
                $item->platform_receive_date = Carbon::createFromTimestamp($item->platform_receive_time)->toDateTimeString();
                $item->user_info = $item->ho_users;
                unset($item->ho_users);
                return $item;
            });
        }
        $e_orders->status_text = self::orderStatusTransformText($e_orders->type, $e_orders->status);
        $e_orders->create_date = Carbon::createFromTimestamp($e_orders->create_time)->toDateTimeString();
        $e_orders->army_receive_date = Carbon::createFromTimestamp($e_orders->army_receive_time)->toDateTimeString();
        return $e_orders;
    }

    /**
     * 分配一个订单到单个供应商
     * @param $order_info .订单
     * @param $supplier_id .供应商id
     * @param $spec_id .规格id
     * @param $allocation_number .分配的数量
     * @param array $data array('confirm_time' => 1512057600, 'warning_time' => 0, 'platform_receive_time' => 1512057600)
     * @return bool
     */
    private function allocationOfferToSupplier($order_info, $supplier_id, $spec_id, $allocation_number, $data = array())
    {
        $supplier_price = SupplierPrice::where('user_id', $supplier_id)->where('spec_id', $spec_id)->first();
        if (empty($order_info) || !$supplier_id || !$supplier_price || !$allocation_number || !MyHelper::is_timestamp($data['confirm_time']) || !MyHelper::is_timestamp($data['platform_receive_time']) || $data['warning_time'] < 0)
        {
            return false;
        }
        /*初始化*/
        $e_order_offer = new OrderOffer();
        $supplier_info = Users::find($supplier_id);
        $sms = new Sms();
        /*时区设置*/
        date_default_timezone_set('PRC');

        /*添加*/
        $e_order_offer->order_id = $order_info->order_id;
        $e_order_offer->user_id = $supplier_id;
        $e_order_offer->status = $this::OFFER_AWAIT_REPLY;
        $e_order_offer->price = $supplier_price->price;
        $e_order_offer->product_number = $allocation_number;
        $e_order_offer->create_time = $GLOBALS['create_time']->timestamp;
        $e_order_offer->platform_receive_time = $data['platform_receive_time'];
        $e_order_offer->confirm_time = $data['confirm_time'];
        $e_order_offer->warning_time = $data['warning_time'];
        $e_order_offer->allocation_user_id = session('ManageUser')->user_id;
        $e_order_offer->save();
        $this::orderLog($order_info->order_id, $supplier_info->nick_name . ' 需供货量:' . $allocation_number . ' (已分配)');

        /*发送短信*/
        $sms->sendSms(Sms::SMS_SIGNATURE_1, Sms::SUPPLIER_RECEIVE_OFFER_CODE, $supplier_info->phone,
            array('date' => now('Asia/Shanghai')->toDateTimeString(), 'order_sn' => $order_info->order_sn, 'confirm_date' => Carbon::createFromTimeStamp($data['confirm_time'], 'Asia/Shanghai')->toDateTimeString()));
        info('短信-供货商收到订单  order ID:' . $order_info->order_id . ' Phone:' . $supplier_info->phone);

        return true;
    }

    /**
     * 分配军方或平台需求
     * @param $arr
     * @param $supplier_arr
     * @return bool
     * @throws \Throwable
     */
    public function allocationSupplier($arr, $supplier_arr)
    {
        /*事物*/
        try
        {
            DB::transaction(function () use ($arr, $supplier_arr)
            {
                /*初始化*/
                $e_orders = Orders::where('order_id', $arr['order_id'])->whereIn('status', [CommonModel::ORDER_AWAIT_ALLOCATION, CommonModel::ORDER_AGAIN_ALLOCATION])->lockForUpdate()->firstOrFail();

                if ($e_orders == false)
                {
                    throw new NetworkBusyException();
                }
                $e_products = Product::checkProduct($e_orders->product_name, $e_orders->spec_name);
                /*更新*/
                $e_orders->status = $this::ORDER_ALREADY_ALLOCATION;/*已分配*/
                $e_orders->platform_allocation_number = !empty($arr['platform_allocation_number']) ? $arr['platform_allocation_number'] : 0;
                $e_orders->save();
                foreach ($supplier_arr as $item)
                {
                    if (!$this->allocationOfferToSupplier($e_orders, $item['supplier_id'], $e_products->spec_info->spec_id, $item['product_number'], array('platform_receive_time' => strtotime($arr['platform_receive_time']), 'confirm_time' => strtotime($arr['confirm_time']), 'warning_time' => $arr['warning_time'])))
                    {
                        throw new SupplierPriceNotFindException();
                    }
                }
                User::userLog('订单ID:' . $e_orders->order_id . ',订单号:' . $e_orders->order_sn);
            });
        } catch (SupplierPriceNotFindException $e)
        {
            $this->errors['code'] = 1;
            $this->errors['messages'] = $e->getMessage();
            return false;
        } catch (NetworkBusyException $e)
        {
            $this->errors['code'] = 2;
            $this->errors['messages'] = $e->getMessage();
            return false;
        } catch (\Exception $e)
        {
            $this->errors['code'] = 3;
            $this->errors['messages'] = '未知错误';
            return false;
        }

        return true;
    }

    /**
     * 平台确认订单
     * @param $order_id
     * @return bool
     * @throws \Throwable
     */
    public function orderConfirm($order_id)
    {
        /*初始化*/
        $sms = new Sms();

        /*事物*/
        try
        {
            DB::transaction(function () use ($order_id, $sms)
            {
                /*初始化*/
                $e_orders = Orders::where('order_id', $order_id)->whereIn('status', [CommonModel::ORDER_ALREADY_ALLOCATION])->lockForUpdate()->firstOrFail();
                if ($e_orders == false)
                {
                    throw new NetworkBusyException();
                }
                /*请求出库Api,出库产品*/
                if ($e_orders->platform_allocation_number > 0 && !$this->outRepertory($e_orders->product_name, $e_orders->spec_name, $e_orders->product_number))
                {
                    throw new OutRepertoryException();
                }
                /*订单状态改变*/
                $e_orders->status = $this::ORDER_ALREADY_CONFIRM;
                $e_orders->save();

                /*发送短信,记录订单日志*/
                $e_order_offer = OrderOffer::where('order_id', '=', $e_orders->order_id)->where('status', $this::OFFER_AWAIT_CONFIRM)->get();
                $e_order_offer->each(function ($item) use ($e_orders, $sms)
                {
                    /*发送短信*/
                    $sms->sendSms(Sms::SMS_SIGNATURE_1, Sms::PLATFORM_CONFIRM_ORDER_CODE, $item->ho_users->phone, array('order_sn' => $e_orders->order_sn));
                    info('短信-平台确认订单提醒  order ID:' . $e_orders->order_id . ' Phone:' . $item->ho_users->phone);
                    $this::orderLog($e_orders->order_id, Users::find($item->user_id)->nick_name . ' 需供货量:' . $item->product_number . ' (已确认)');
                });
                /*修改报价状态*/
                OrderOffer::where('order_id', '=', $e_orders->order_id)
                    ->where('status', $this::OFFER_AWAIT_CONFIRM)
                    ->update(['status' => $this::OFFER_AWAIT_SEND]);
                /*记录操作日志*/
                User::userLog('订单ID:' . $e_orders->order_id . ',订单号:' . $e_orders->order_sn);
            });
        } catch (NetworkBusyException $e)
        {
            $this->errors['code'] = 1;
            $this->errors['messages'] = $e->getMessage();
            return false;
        } catch (OutRepertoryException $e)
        {
            $this->errors['code'] = 2;
            $this->errors['messages'] = $e->getMessage();
            return false;
        } catch (\Exception $e)
        {
            $this->errors['code'] = 3;
            $this->errors['messages'] = '未知错误';
            return false;
        }

        return true;
    }

    /**
     * 发布平台需求
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
        $e_orders = new Orders();
        /*时区设置*/
        date_default_timezone_set('PRC');

        /*添加*/
        $e_orders->type = self::ORDER_TYPE_PLATFORM;
        $e_orders->status = $this::ORDER_AWAIT_ALLOCATION;
        $e_orders->order_sn = $this->makeOrderSn();
        $e_orders->category_id = $product->category_id;
        $e_orders->product_thumb = $product->product_thumb;
        $e_orders->product_name = $product->product_name;
        $e_orders->spec_name = $product->spec_info->spec_name;
        $e_orders->spec_unit = $product->spec_info->spec_unit;
        $e_orders->product_number = !empty($arr['product_number']) ? $arr['product_number'] : 1;
        $e_orders->army_contact_person = '';
        $e_orders->army_contact_tel = '';
        $e_orders->army_note = '';
        $e_orders->platform_allocation_number = 0;
        $e_orders->army_receive_time = 0;/*2017-10-18 08:45:12*/
        $e_orders->create_time = Carbon::now('PRC')->timestamp;
        $e_orders->quality_check = $this::ORDER_NO_QUALITY_CHECK;
        $e_orders->is_delete = $this::ORDER_NO_DELETE;
        $e_orders->army_id = null;
        $e_orders->save();

        /*清除购物车该产品*/
        ShoppingCart::where('user_id', session('ManageUser')->user_id)->where('product_name', $e_orders->product_name)->where('spec_name', $e_orders->spec_name)->delete();

        User::userLog('订单ID:' . $e_orders->order_id . ',订单号:' . $e_orders->order_sn);
        return true;
    }

    /**
     * 平台 库存供应 单个订单
     * @param $order_id
     * @return bool
     */
    public function inventorySupplyNeed($order_id)
    {
        $e_orders = Orders::where('order_id', $order_id)->where('type', Army::ORDER_TYPE_ARMY)->where('status', CommonModel::ORDER_AWAIT_ALLOCATION)->firstOrFail();

        $e_orders->status = CommonModel::ORDER_ALLOCATION_PLATFORM;
        $e_orders->quality_check = CommonModel::ORDER_IS_QUALITY_CHECK;
        $e_orders->save();
        User::userLog('订单ID:' . $e_orders->order_id . ',订单号:' . $e_orders->order_sn);
        return true;
    }

    /**
     * 平台确认收到供应商的供货
     * @param $order_id
     * @param $offer_id
     * @return bool
     * @throws \Throwable
     */
    public function supplierConfirmReceive($order_id, $offer_id)
    {
        $sms = new Sms();
        /*事物*/
        try
        {
            DB::transaction(function () use ($order_id, $offer_id, $sms)
            {

                $e_orders = Orders::where('order_id', $order_id)->whereIn('status', [$this::ORDER_ALREADY_CONFIRM])->firstOrFail();
                $e_order_offer = OrderOffer::where('offer_id', $offer_id)->where('status', $this::OFFER_ALREADY_SEND)->firstOrFail();
                $e_users = $e_order_offer->ho_users()->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)->firstOrFail();

                /*更新报价状态*/
                $e_order_offer->status = $this::OFFER_ALREADY_RECEIVE;
                $e_order_offer->save();
                $this::orderLog($e_orders->order_id, $e_users->nick_name . ' 需供货量:' . $e_order_offer->product_number . ' (已确认收货)');

                /*发送短信 给供应商*/
                $sms->sendSms(Sms::SMS_SIGNATURE_1, Sms::PLATFORM_CONFIRM_RECEIVE_CODE, $e_users->phone, array('order_sn' => $e_orders->order_sn));
                info('短信-平台确认收货  order ID:' . $e_orders->order_id . ' Phone:' . $e_users->phone);

                /*验证是否所有供货商都已经到货*/
                $validate_offer = OrderOffer::where('order_id', $e_orders->order_id)->whereIn('status', [$this::OFFER_AWAIT_SEND, $this::OFFER_ALREADY_SEND])->get();
                if ($validate_offer->isEmpty())
                {
                    /*更新订单状态,及质检*/
                    //军方订单
                    if ($e_orders->type == Army::ORDER_TYPE_ARMY)
                    {
                        $e_orders->status = $this::ORDER_ALREADY_RECEIVE;
                        $e_orders->quality_check = $this::ORDER_IS_QUALITY_CHECK;
                        $e_orders->save();
                    }
                    //平台订单
                    else if ($e_orders->type == Platform::ORDER_TYPE_PLATFORM)
                    {
                        $e_orders->status = $this::ORDER_SUCCESSFUL;
                        $e_orders->quality_check = $this::ORDER_IS_QUALITY_CHECK;
                        $e_orders->save();
                    }
                    else
                    {
                        throw new \Exception();
                    }
                }
                User::userLog('订单ID:' . $e_orders->order_id . ',订单号:' . $e_orders->order_sn);
            });
        } catch (\Exception $e)
        {
            info($e);
            $this->errors['code'] = 1;
            $this->errors['messages'] = '未知错误';
            return false;
        }

        return true;
    }

    /**
     * 发货到军方
     * @param $order_id
     * @return bool
     */
    public function platformSendArmy($order_id)
    {
        $e_orders = Orders::where('order_id', $order_id)->whereIn('status', [CommonModel::ORDER_ALREADY_RECEIVE, CommonModel::ORDER_ALLOCATION_PLATFORM])->firstOrFail();

        $e_orders->status = CommonModel::ORDER_SEND_ARMY;
        $e_orders->save();
        User::userLog('订单ID:' . $e_orders->order_id . ',订单号:' . $e_orders->order_sn);
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
                    $text = '需再分配';
                    break;
                case $this::ORDER_ALREADY_ALLOCATION:
                    $text = '待确认';
                    break;
                case $this::ORDER_ALREADY_CONFIRM:
                    $text = '待确认到货';
                    break;
                case $this::ORDER_ALREADY_RECEIVE:
                    $text = '供应商全部已到货';
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
                case $this::ORDER_AWAIT_ALLOCATION:
                    $text = '待分配';
                    break;
                case $this::ORDER_AGAIN_ALLOCATION:
                    $text = '需再分配';
                    break;
                case $this::ORDER_ALREADY_ALLOCATION:
                    $text = '待确认';
                    break;
                case $this::ORDER_ALREADY_CONFIRM:
                    $text = '待确认到货';
                    break;
                case $this::ORDER_SUCCESSFUL:
                    $text = '平台已收货(交易成功)';
                    break;
            }
        }
        return $text;
    }
}