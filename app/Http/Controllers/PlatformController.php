<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */
namespace App\Http\Controllers;

use App\Entity\Orders;
use App\Models\Army;
use App\Models\CommonModel;
use App\Models\Platform;
use App\Models\Product;
use App\Models\Sms;
use App\Models\User;
use App\Tools\M3Result;
use App\Tools\MyHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


/**
 * 平台控制器
 * Class PlatformController
 * @package App\Http\Controllers
 */
class PlatformController extends Controller
{
    public $ViewData = array(); /*传递页面的数组*/

    /**
     * View 平台订单列表 页面 (搜索条件参数: 订单类型, 订单状态, 创建时间)
     * @param int $type
     * @param string $status
     * @param string $create_time
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function NeedList($type = 0, $status = 'null', $create_time = 'null')
    {
        /*初始化*/
        $platform = new Platform();
        $where = array();
        $this->ViewData['order_list'] = array();

        /*条件搜索*/
        switch ($type)
        {
            case Platform::ORDER_TYPE_PLATFORM :
                array_push($where, ['orders.type', '=', Platform::ORDER_TYPE_PLATFORM]);
                break;
            case Army::ORDER_TYPE_ARMY :
                array_push($where, ['orders.type', '=', Army::ORDER_TYPE_ARMY]);
                break;
        }
        switch ($status)
        {
            case '待分配' :
                array_push($where, ['orders.status', '!=', $platform::ORDER_ALLOCATION_SUPPLIER]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_SUPPLIER_SELECTED]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_SUPPLIER_SEND]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_SUPPLIER_RECEIVE]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_ALLOCATION_PLATFORM]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_SEND_ARMY]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_SUCCESSFUL]);
                break;
            case '已分配':
                array_push($where, ['orders.status', '!=', $platform::ORDER_ALLOCATION_PLATFORM]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_SEND_ARMY]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_SUCCESSFUL]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_AWAIT_ALLOCATION]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_AGAIN_ALLOCATION]);
                break;
            case '库存供应' :
                array_push($where, ['orders.status', '=', $platform::ORDER_ALLOCATION_PLATFORM]);
                break;
            case '交易成功' :
                array_push($where, ['orders.status', '=', $platform::ORDER_SUCCESSFUL]);
                break;
        }
        if (!empty($create_time) && strtotime($create_time))
        {
            $dt = Carbon::parse($create_time);
            $start_dt = Carbon::create($dt->year, $dt->month, $dt->day, 0, 0, 0)->timestamp;
            $end_dt = Carbon::create($dt->year, $dt->month, $dt->day, 0, 0, 0)->addDay()->subSecond()->timestamp;
            array_push($where, ['orders.create_time', '>=', $start_dt]);
            array_push($where, ['orders.create_time', '<=', $end_dt]);
        }
        $this->ViewData['order_list'] = $platform->getOrderList($where);
        $this->ViewData['page_search'] = array('type' => $type, 'status' => $status, 'create_time' => $create_time);

//        dump($this->ViewData);
        return view('platform_need_list', $this->ViewData);
    }

    /**
     * View 平台发布需求 页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function NeedView()
    {
        /*初始化*/
        $user = new User();
        $product = new Product();
        $this->ViewData['supplier_list'] = $user->getSupplierList();
        $this->ViewData['unit_list'] = $product->getProductCategoryUnitList();
        $this->ViewData['product_category'] = $product->getProductCategoryList(array(), array(['product_category.sort', 'desc']), false);

//        dump($this->ViewData);
        return view('platform_need_view', $this->ViewData);
    }

    /**
     * View 平台查看报价 页面
     * @param $order_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function OfferInfoView($order_id)
    {
        /*初始化*/
        $platform = new Platform();
        $this->ViewData['order_info'] = $platform->getOrderInfo($order_id);
        $this->ViewData['count_down'] = $this->ViewData['order_info']['status'] == 100 ? bcsub($this->ViewData['order_info']['offer_info'][0]['confirm_time'], now()->timestamp) : 0;

//        dump($this->ViewData);
        return view('platform_offer_view', $this->ViewData);
    }

    /**
     * View 平台分配供货商 页面
     * @param $order_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function OfferAllocationView($order_id)
    {
        /*初始化*/
        $user = new User();
        $platform = new Platform();

        $this->ViewData['supplier_list'] = $user->getSupplierList();
        $this->ViewData['order_info'] = $platform->getOrderInfo($order_id);

//        dump($this->ViewData);
        return view('platform_allocation_view', $this->ViewData);
    }

    /**
     * Ajax 平台分配供货商 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function OfferAllocation(Request $request)
    {
        /*初始化*/
        $m3result = new M3Result();
        $platform = new Platform();

        /*验证规则*/
        $rules = [
            'warning_time' => 'required|integer',
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders')->where(function ($query)
                {
                    $query->where('order_id', $GLOBALS['request']->input('order_id'))->whereIn('type', [Army::ORDER_TYPE_ARMY, Platform::ORDER_TYPE_PLATFORM])->where('is_delete', CommonModel::ORDER_NO_DELETE)
                        ->whereIn('status', [CommonModel::ORDER_AWAIT_ALLOCATION, CommonModel::ORDER_AGAIN_ALLOCATION]);
                }),
            ],
            'platform_receive_time' => 'required',
            'confirm_time' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        $order_info = Orders::find($request->input('order_id'));

        /*平台订单 平台到货时间 增加规则*/
        $validator->sometimes('platform_receive_time', ['date', 'after:now'], function ($input) use ($order_info)
        {
            return $order_info->type === Platform::ORDER_TYPE_PLATFORM;
        });

        /*军方订单 平台到货时间 增加规则*/
        $validator->sometimes('platform_receive_time', ['date', 'after:now', 'before:' . date('YmdHis', $order_info->army_receive_time)], function ($input) use ($order_info)
        {
            return $order_info->type === Army::ORDER_TYPE_ARMY;
        });

        /*订单确认时间 增加规则*/
        $validator->sometimes('confirm_time', ['date', 'before_or_equal:' . $request->input('platform_receive_time')], function ($input) use ($order_info)
        {
            return !empty($input->platform_receive_time);
        });

        /*供货商A增加规则*/
        $validator->sometimes('supplier_A', ['required', 'integer',
            Rule::exists('users', 'user_id')->where('user_id', $request->input('supplier_A'))->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)
        ], function ($input)
        {
            return !empty($input->supplier_A);
        });

        /*供货商B增加规则*/
        $validator->sometimes('supplier_B', ['required', 'integer',
            Rule::exists('users', 'user_id')->where('user_id', $request->input('supplier_B'))->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)
        ], function ($input)
        {
            return !empty($input->supplier_B);
        });

        /*供货商C增加规则*/
        $validator->sometimes('supplier_C', ['required', 'integer',
            Rule::exists('users', 'user_id')->where('user_id', $request->input('supplier_C'))->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)
        ], function ($input)
        {
            return !empty($input->supplier_C);
        });

        $supplier_arr = collect([$request->input('supplier_A'), $request->input('supplier_B'), $request->input('supplier_C')])->filter()->unique()->all();
        if (!empty($supplier_arr) && $validator->passes() && $platform->allocationSupplier($request->all(), $supplier_arr))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '分配供货商成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['platform'] = $platform->messages();
            if ($m3result->data['platform']['code'] == 2)
            {
                $m3result->code = 2;
                $m3result->messages = $m3result->data['platform']['messages'];
            }
        }
        return $m3result->toJson();
    }

    /**
     * Ajax 平台选择供货商 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function OfferSelected(Request $request)
    {
        /*初始化*/
        $m3result = new M3Result();
        $platform = new Platform();

        /*验证规则*/
        $rules = [
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders')->where(function ($query)
                {
                    $query->where('order_id', $GLOBALS['request']->input('order_id'))->where('is_delete', CommonModel::ORDER_NO_DELETE)
                        ->whereIn('status', [CommonModel::ORDER_ALLOCATION_SUPPLIER]);
                }),
            ],
            'offer_id' => [
                'required',
                'integer',
                Rule::exists('order_offer')->where(function ($query)
                {
                    $query->where('offer_id', $GLOBALS['request']->input('offer_id'))->where('order_id', $GLOBALS['request']->input('order_id'))
                        ->where('status', CommonModel::OFFER_AWAIT_PASS)->where('create_time', '>=', Orders::find($GLOBALS['request']->input('order_id'))->create_time);
                }),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $platform->selectedSupplier($request->input('order_id'), $request->input('offer_id')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '选择供货商成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['platform'] = $platform->messages();
        }

        return $m3result->toJson();
    }

    /**
     * Ajax 平台库存供应 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function InventorySupply(Request $request)
    {
        /*初始化*/
        $m3result = new M3Result();
        $platform = new Platform();

        /*验证规则*/
        $rules = [
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders')->where(function ($query)
                {
                    $query->where('order_id', $GLOBALS['request']->input('order_id'))->where('type', Army::ORDER_TYPE_ARMY)->where('is_delete', CommonModel::ORDER_NO_DELETE)
                        ->whereIn('status', [CommonModel::ORDER_AWAIT_ALLOCATION, CommonModel::ORDER_AGAIN_ALLOCATION]);
                }),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $platform->inventorySupplyNeed($request->input('order_id')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '平台库存供应';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['platform'] = $platform->messages();
        }
        return $m3result->toJson();
    }

    /**
     * Ajax 供应商确认收货 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ConfirmReceive(Request $request)
    {
        /*初始化*/
        $m3result = new M3Result();
        $platform = new Platform();

        /*验证规则*/
        $rules = [
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders')->where(function ($query)
                {
                    $query->where('order_id', $GLOBALS['request']->input('order_id'))->where('is_delete', CommonModel::ORDER_NO_DELETE)
                        ->whereIn('status', [CommonModel::ORDER_SUPPLIER_SEND]);
                }),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $platform->supplierConfirmReceive($request->input('order_id')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '确认收货成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['platform'] = $platform->messages();
        }
        return $m3result->toJson();
    }

    /**
     * Ajax 发货到军方 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function SendArmy(Request $request)
    {
        /*初始化*/
        $m3result = new M3Result();
        $platform = new Platform();

        /*验证规则*/
        $rules = [
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders')->where(function ($query)
                {
                    $query->where('order_id', $GLOBALS['request']->input('order_id'))->where('is_delete', CommonModel::ORDER_NO_DELETE)
                        ->whereIn('status', [CommonModel::ORDER_SUPPLIER_RECEIVE, CommonModel::ORDER_ALLOCATION_PLATFORM]);
                }),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $platform->platformSendArmy($request->input('order_id')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '已发货到军方';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['platform'] = $platform->messages();
        }
        return $m3result->toJson();
    }

    /**
     * Ajax 平台发布需求 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function NeedRelease(Request $request)
    {
        /*初始化*/
        $platform = new Platform();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'product_name' => 'required',
            'product_number' => 'required|integer|min:1',
            'product_unit' => 'required',
            'platform_receive_time' => 'required|date|after:now',
            'confirm_time' => 'required|date|before_or_equal:' . $request->input('platform_receive_time')
        ];
        $validator = Validator::make($request->all(), $rules);
        /*供货商A增加规则*/
        $validator->sometimes('supplier_A', [
            'required',
            'integer',
            Rule::exists('users', 'user_id')->where('user_id', $request->input('supplier_A'))->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)
        ], function ($input)
        {
            return !empty($input->supplier_A);/*return true时才增加验证规则!*/
        });
        /*供货商B增加规则*/
        $validator->sometimes('supplier_B', [
            'required',
            'integer',
            Rule::exists('users', 'user_id')->where('user_id', $request->input('supplier_B'))->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)
        ], function ($input)
        {
            return !empty($input->supplier_B);/*return true时才增加验证规则!*/
        });
        /*供货商C增加规则*/
        $validator->sometimes('supplier_C', [
            'required',
            'integer',
            Rule::exists('users', 'user_id')->where('user_id', $request->input('supplier_C'))->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)
        ], function ($input)
        {
            return !empty($input->supplier_C);/*return true时才增加验证规则!*/
        });

        $supplier_arr = collect([$request->input('supplier_A'), $request->input('supplier_B'), $request->input('supplier_C')])->filter()->unique()->all();
        if (!empty($supplier_arr) && $validator->passes() && $platform->releaseNeed($request->all(), $supplier_arr))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '平台需求发布成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['platform'] = $platform->messages();
        }

        return $m3result->toJson();
    }

}