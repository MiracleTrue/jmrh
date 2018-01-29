<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */
namespace App\Http\Controllers;

use App\Entity\Orders;
use App\Exceptions\SupplierPriceNotFindException;
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
        return view('platform_order_list', $this->ViewData);
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
     * View 平台订单确认 页面
     * @param $order_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function OrderConfirmView($order_id)
    {
        /*初始化*/
        $platform = new Platform();
        $order_info = $platform->getOrderInfo($order_id);
        $this->ViewData['offer_list'] = $order_info->offer_info->groupBy('create_date')->values();
        $this->ViewData['count_down'] = 0;
        $this->ViewData['button'] = '等待';

        if ($order_info['status'] == 100)
        {
            $await_offer = $order_info->offer_info->filter(function ($value)
            {
                return $value->status == CommonModel::OFFER_AWAIT_REPLY;
            })->values();

            if ($await_offer->isEmpty())
            {
                $this->ViewData['button'] = ' 确认';
            }
            else
            {
                $this->ViewData['count_down'] = bcsub($await_offer[0]['confirm_time'], now()->timestamp);
            }
        }
        elseif ($order_info['status'] == 1)
        {
            $this->ViewData['button'] = '重新分配';
        }


        return view('platform_order_confirm_view', $this->ViewData);
    }

    /**
     * View 平台确认收货 页面
     * @param $order_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ConfirmReceiveView($order_id)
    {
        /*初始化*/
        $platform = new Platform();
        $order_info = $platform->getOrderInfo($order_id);
        $confirm_offer = $order_info->offer_info->filter(function ($value)
        {
            return in_array($value->status, [CommonModel::OFFER_AWAIT_SEND, CommonModel::OFFER_ALREADY_SEND, CommonModel::OFFER_ALREADY_RECEIVE]);
        })->values();

        $this->ViewData['order_info'] = $order_info;
        $this->ViewData['offer_list'] = $confirm_offer;

        return view('platform_order_receive_view', $this->ViewData);
    }

    /**
     * View 平台(首次)分配供货商 页面
     * @param $order_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function OfferAllocationView($order_id)
    {
        /*初始化*/
        $user = new User();
        $platform = new Platform();
        $order_info = $platform->getOrderInfo($order_id);

        $this->ViewData['supplier_list'] = $user->getAllocationProductSupplierList($order_info->product_name, $order_info->spec_name);
        $this->ViewData['order_info'] = $order_info;
        $this->ViewData['repertory_info'] = $platform->getRepertory($this->ViewData['order_info']->product_name, $this->ViewData['order_info']->spec_name);

        return view('platform_allocation_view', $this->ViewData);
    }

    /**
     * View 平台(二次)分配供货商 页面
     * @param $order_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function OfferReAllocationView($order_id)
    {
        /*初始化*/
        $user = new User();
        $platform = new Platform();
        $order_info = $platform->getOrderInfo($order_id);

        $this->ViewData['supplier_list'] = $user->getAllocationProductSupplierList($order_info->product_name, $order_info->spec_name);
        $this->ViewData['order_info'] = $order_info;
        $this->ViewData['repertory_info'] = $platform->getRepertory($this->ViewData['order_info']->product_name, $this->ViewData['order_info']->spec_name);
        $confirm_offer = $order_info->offer_info->filter(function ($value)
        {
            return $value->status == CommonModel::OFFER_AWAIT_CONFIRM;
        });
        $this->ViewData['need_number'] = bcsub($order_info->product_number, $confirm_offer->sum('product_number'), 2);

        return view('platform_re_allocation_view', $this->ViewData);
    }

    /**
     * Ajax 平台首次分配供应商 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function OfferAllocation(Request $request)
    {
        $arr = array(
            'order_id' => 7,
            'confirm_time' => '2018-2-3',
            'platform_receive_time' => '2018-2-3',
            'supplier_A_id' => '5',
            'supplier_A_number' => '200',
            'supplier_B_id' => '3',
            'supplier_B_number' => '300',


            'platform_allocation_number' => '100',
            'warning_time' => '0'
        );
        $request->merge($arr);

        /*初始化*/
        $m3result = new M3Result();
        $platform = new Platform();
        $order_info = $platform->getOrderInfo($request->input('order_id'));

        /*验证规则*/
        $rules = [
            'warning_time' => 'required|integer',
            'platform_receive_time' => 'required',
            'supplier_A_number' => 'sometimes|numeric',
            'supplier_B_number' => 'sometimes|numeric',
            'supplier_C_number' => 'sometimes|numeric',
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders')->where(function ($query)
                {
                    $query->where('order_id', $GLOBALS['request']->input('order_id'))->whereIn('type', [Army::ORDER_TYPE_ARMY, Platform::ORDER_TYPE_PLATFORM])
                        ->where('status', CommonModel::ORDER_AWAIT_ALLOCATION);
                })
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        /*军方订单库存必填*/
        $validator->sometimes('platform_allocation_number', ['required', 'numeric'], function ($input) use ($order_info)
        {
            return $order_info->type === Army::ORDER_TYPE_ARMY;
        });

        /*平台订单 平台到货时间 增加规则*/
        $validator->sometimes('platform_receive_time', ['date', 'after:now'], function ($input) use ($order_info)
        {
            return $order_info->type === Platform::ORDER_TYPE_PLATFORM;
        });

        /*军方订单 平台到货时间 增加规则*/
        $validator->sometimes('platform_receive_time', ['date', 'after:now', 'before:' . $order_info->army_receive_time], function ($input) use ($order_info)
        {
            return $order_info->type === Army::ORDER_TYPE_ARMY;
        });

        /*订单确认时间 增加规则*/
        $validator->sometimes('confirm_time', ['date', 'after:now', 'before_or_equal:' . $request->input('platform_receive_time')], function ($input) use ($order_info)
        {
            return !empty($input->platform_receive_time);
        });

        /*供货商A增加规则*/
        $validator->sometimes('supplier_A_id', ['required', 'integer',
            Rule::exists('users', 'user_id')->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)
        ], function ($input)
        {
            return !empty($input->supplier_A_id);
        });

        /*供货商B增加规则*/
        $validator->sometimes('supplier_B_id', ['required', 'integer',
            Rule::exists('users', 'user_id')->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)
        ], function ($input)
        {
            return !empty($input->supplier_B_id);
        });

        /*供货商C增加规则*/
        $validator->sometimes('supplier_C_id', ['required', 'integer',
            Rule::exists('users', 'user_id')->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)
        ], function ($input)
        {
            return !empty($input->supplier_C_id);
        });

        /*供应商信息过滤验证*/
        $supplier_arr = collect([
            [
                'supplier_id' => $request->input('supplier_A_id'),
                'product_number' => $request->input('supplier_A_number'),
            ],
            [
                'supplier_id' => $request->input('supplier_B_id'),
                'product_number' => $request->input('supplier_B_number'),
            ],
            [
                'supplier_id' => $request->input('supplier_C_id'),
                'product_number' => $request->input('supplier_C_number'),
            ],
        ])->filter(function ($value, $key)
        {
            return !empty($value['supplier_id']) && !empty($value['product_number']);
        })->unique('supplier_id')->all();

        /*(首次) 分配数量与库存数量验证*/
        $product_info = Product::checkProduct($order_info['product_name'], $order_info['spec_name']);
        //军方订单
        if ($order_info->type === Army::ORDER_TYPE_ARMY)
        {
            $repertory_info = $platform->getRepertory($order_info['product_name'], $order_info['spec_name']);
            $residue_number = bcsub($order_info->product_number, $request->input('platform_allocation_number'), 2);/*减去库存供应量*/
            $residue_number = bcsub($residue_number, collect($supplier_arr)->sum('product_number'), 2);/*减去供货商供应量*/
            if (!$product_info || !$repertory_info || $residue_number != 0.00)
            {
                $m3result->code = 2;
                $m3result->messages = '分配数量不正确或产品不存在';
                return $m3result->toJson();
            }
        }
        //平台订单
        elseif ($order_info->type === Platform::ORDER_TYPE_PLATFORM)
        {

        }

        if (!empty($supplier_arr) && $validator->passes() && $platform->allocationSupplier($request->all(), $supplier_arr))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '分配成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['platform'] = $platform->messages();
            if ($m3result->data['platform']['code'] == 2)
            {
                $m3result->code = 3;
                $m3result->messages = $m3result->data['platform']['messages'];
            }
        }
        return $m3result->toJson();
    }

    /**
     * Ajax 平台二次分配供应商 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function OfferReAllocation(Request $request)
    {
        $arr = array(
            'order_id' => 7,
            'confirm_time' => '2018-2-3',
            'platform_receive_time' => '2018-2-3',
            'supplier_A_id' => '5',
            'supplier_A_number' => '50',
            'supplier_B_id' => '3',
            'supplier_B_number' => '50',

            'platform_allocation_number' => '300',
            'warning_time' => '0'
        );
        $request->merge($arr);

        /*初始化*/
        $m3result = new M3Result();
        $platform = new Platform();
        $order_info = $platform->getOrderInfo($request->input('order_id'));

        /*验证规则*/
        $rules = [
            'warning_time' => 'required|integer',
            'platform_receive_time' => 'required',
            'supplier_A_number' => 'sometimes|numeric',
            'supplier_B_number' => 'sometimes|numeric',
            'supplier_C_number' => 'sometimes|numeric',
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders')->where(function ($query)
                {
                    $query->where('order_id', $GLOBALS['request']->input('order_id'))->whereIn('type', [Army::ORDER_TYPE_ARMY, Platform::ORDER_TYPE_PLATFORM])
                        ->where('status', CommonModel::ORDER_AGAIN_ALLOCATION);
                })
            ]
        ];

        $validator = Validator::make($request->all(), $rules);

        /*军方订单库存必填*/
        $validator->sometimes('platform_allocation_number', ['required', 'numeric'], function ($input) use ($order_info)
        {
            return $order_info->type === Army::ORDER_TYPE_ARMY;
        });

        /*平台订单 平台到货时间 增加规则*/
        $validator->sometimes('platform_receive_time', ['date', 'after:now'], function ($input) use ($order_info)
        {
            return $order_info->type === Platform::ORDER_TYPE_PLATFORM;
        });

        /*军方订单 平台到货时间 增加规则*/
        $validator->sometimes('platform_receive_time', ['date', 'after:now', 'before:' . $order_info->army_receive_time], function ($input) use ($order_info)
        {
            return $order_info->type === Army::ORDER_TYPE_ARMY;
        });

        /*订单确认时间 增加规则*/
        $validator->sometimes('confirm_time', ['date', 'after:now', 'before_or_equal:' . $request->input('platform_receive_time')], function ($input) use ($order_info)
        {
            return !empty($input->platform_receive_time);
        });

        /*供货商A增加规则*/
        $validator->sometimes('supplier_A_id', ['required', 'integer',
            Rule::exists('users', 'user_id')->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)
        ], function ($input)
        {
            return !empty($input->supplier_A_id);
        });

        /*供货商B增加规则*/
        $validator->sometimes('supplier_B_id', ['required', 'integer',
            Rule::exists('users', 'user_id')->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)
        ], function ($input)
        {
            return !empty($input->supplier_B_id);
        });

        /*供货商C增加规则*/
        $validator->sometimes('supplier_C_id', ['required', 'integer',
            Rule::exists('users', 'user_id')->where('is_disable', User::NO_DISABLE)->where('identity', User::SUPPLIER_ADMIN)
        ], function ($input)
        {
            return !empty($input->supplier_C_id);
        });

        /*供应商信息过滤验证*/
        $supplier_arr = collect([
            [
                'supplier_id' => $request->input('supplier_A_id'),
                'product_number' => $request->input('supplier_A_number'),
            ],
            [
                'supplier_id' => $request->input('supplier_B_id'),
                'product_number' => $request->input('supplier_B_number'),
            ],
            [
                'supplier_id' => $request->input('supplier_C_id'),
                'product_number' => $request->input('supplier_C_number'),
            ],
        ])->filter(function ($value)
        {
            return !empty($value['supplier_id']) && !empty($value['product_number']);
        })->unique('supplier_id')->all();

        /*(二次) 分配数量与库存数量验证*/
        $product_info = Product::checkProduct($order_info['product_name'], $order_info['spec_name']);
        //军方订单
        if ($order_info->type === Army::ORDER_TYPE_ARMY)
        {
            $confirm_offer = $order_info->offer_info->filter(function ($value)
            {
                return $value->status == CommonModel::OFFER_AWAIT_CONFIRM;
            });

            $repertory_info = $platform->getRepertory($order_info['product_name'], $order_info['spec_name']);
            $residue_number = bcsub($order_info->product_number, $confirm_offer->sum('product_number'), 2);/*减去已经确认的报价*/
            $residue_number = bcsub($residue_number, $request->input('platform_allocation_number'), 2);/*减去库存供应量*/
            $residue_number = bcsub($residue_number, collect($supplier_arr)->sum('product_number'), 2);/*减去供货商供应量*/
            if (!$product_info || !$repertory_info || $residue_number != 0.00)
            {
                $m3result->code = 2;
                $m3result->messages = '分配数量不正确或产品不存在';
                return $m3result->toJson();
            }
        }
        //平台订单
        elseif ($order_info->type === Platform::ORDER_TYPE_PLATFORM)
        {

        }

        if ($validator->passes() && $platform->allocationSupplier($request->all(), $supplier_arr))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '分配成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['platform'] = $platform->messages();
            if ($m3result->data['platform']['code'] == 2)
            {
                $m3result->code = 3;
                $m3result->messages = $m3result->data['platform']['messages'];
            }
        }
        return $m3result->toJson();
    }

    /**
     * Ajax 平台确认订单 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function OrderConfirm(Request $request)
    {
        $arr = array(
            'order_id' => '7',
        );
        $request->merge($arr);
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
                    $query->where('order_id', $GLOBALS['request']->input('order_id'))->whereIn('status', [CommonModel::ORDER_ALREADY_ALLOCATION]);
                }),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes())
        {   /*验证通过*/
            $order_info = $platform->getOrderInfo($request->input('order_id'));
            $confirm_offer = $order_info->offer_info->filter(function ($value)
            {
                return $value->status == CommonModel::OFFER_AWAIT_CONFIRM;
            });

            if ($order_info->platform_allocation_number > 0)
            {   /*有库存供应,验证数量*/
                $repertory_info = $platform->getRepertory($order_info->product_name, $order_info->spec_name);
                if (!$repertory_info || bcsub($repertory_info['number'], $order_info->platform_allocation_number, 2) <= 0)
                {
                    $m3result->code = 3;
                    $m3result->messages = '库存供应数量不足';
                }
                else if (bcadd($confirm_offer->sum('product_number'), $order_info->platform_allocation_number, 2) != $order_info->product_number)
                {
                    $m3result->code = 2;
                    $m3result->messages = '分配数量不正确';
                }
                else
                {
                    if ($platform->orderConfirm($request->input('order_id')) == true)
                    {
                        $m3result->code = 0;
                        $m3result->messages = '确认订单成功,已通知供应商发货';
                    }
                    else
                    {
                        $m3result->code = 1;
                        $m3result->messages = '数据验证失败';
                        $m3result->data['validator'] = $validator->messages();
                        $m3result->data['platform'] = $platform->messages();
                    }
                }
            }
            else
            {   /*无库存供应*/
                if (bcsub($order_info->product_number, $confirm_offer->sum('product_number'), 2) != 0)
                {
                    $m3result->code = 2;
                    $m3result->messages = '分配数量不正确';
                }
                else
                {
                    if ($platform->orderConfirm($request->input('order_id')) == true)
                    {
                        $m3result->code = 0;
                        $m3result->messages = '确认订单成功,已通知供应商发货';
                    }
                    else
                    {
                        $m3result->code = 1;
                        $m3result->messages = '数据验证失败';
                        $m3result->data['validator'] = $validator->messages();
                        $m3result->data['platform'] = $platform->messages();
                    }
                }
            }
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
                    $query->where('order_id', $GLOBALS['request']->input('order_id'))->where('type', Army::ORDER_TYPE_ARMY)->where('status', CommonModel::ORDER_AWAIT_ALLOCATION);
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
     * Ajax 平台确认收货 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ConfirmReceive(Request $request)
    {
//        $arr = array(
//            'order_id' => '7',
//            'offer_id' => 6,
//        );
//        $request->merge($arr);
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
                    $query->where('order_id', $GLOBALS['request']->input('order_id'))->whereIn('status', [CommonModel::ORDER_ALREADY_CONFIRM]);
                })
            ],
            'offer_id' => [
                'required',
                'integer',
                Rule::exists('order_offer')->where(function ($query)
                {
                    $query->where('offer_id', $GLOBALS['request']->input('offer_id'))->whereIn('status', [CommonModel::OFFER_ALREADY_SEND]);
                })
            ],
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $platform->supplierConfirmReceive($request->input('order_id'), $request->input('offer_id')))
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
                    $query->where('order_id', $GLOBALS['request']->input('order_id'))->whereIn('status', [CommonModel::ORDER_ALREADY_RECEIVE, CommonModel::ORDER_ALLOCATION_PLATFORM]);
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