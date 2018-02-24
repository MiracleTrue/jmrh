<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */
namespace App\Http\Controllers;

use App\Entity\OrderOffer;
use App\Entity\Orders;
use App\Models\Army;
use App\Models\Cart;
use App\Models\CommonModel;
use App\Models\Platform;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Tools\M3Result;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;


/**
 * 平台控制器
 * Class PlatformController
 * @package App\Http\Controllers
 */
class PlatformController extends Controller
{
    public $ViewData = array(); /*传递页面的数组*/

    /**
     * View 平台统计 页面 (搜索条件参数: 开始时间, 结束时间)
     * @param string $start_date
     * @param string $end_date
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function Statistics($start_date = 'null', $end_date = 'null')
    {
        /*初始化*/
        $platform = new Platform();
        $time_where = array();

        if (strtotime($start_date) && strtotime($end_date))
        {
            $start_dt = Carbon::parse($start_date);
            $end_dt = Carbon::parse($end_date);
            array_push($time_where, ['order_offer.create_time', '>=', $start_dt->timestamp]);
            array_push($time_where, ['order_offer.create_time', '<=', $end_dt->timestamp]);
        }
        $this->ViewData['list'] = $platform->getStatistics($time_where);
        $this->ViewData['page_search'] = array('start_date' => $start_date, 'end_date' => $end_date);

        return view('platform_statistics_list', $this->ViewData);
    }

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
                array_push($where, ['orders.status', '!=', $platform::ORDER_ALREADY_ALLOCATION]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_ALREADY_CONFIRM]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_ALREADY_RECEIVE]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_ALLOCATION_PLATFORM]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_SEND_ARMY]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_SUCCESSFUL]);
                break;
            case '已分配':
                array_push($where, ['orders.status', '!=', $platform::ORDER_SEND_ARMY]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_SUCCESSFUL]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_AWAIT_ALLOCATION]);
                array_push($where, ['orders.status', '!=', $platform::ORDER_AGAIN_ALLOCATION]);
                break;
            case '已发货':
                array_push($where, ['orders.status', '=', $platform::ORDER_SEND_ARMY]);
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

        return view('platform_order_list', $this->ViewData);
    }

    /**
     * View 平台发布需求 页面
     * @param string $cart_ids 购物车id [5,6,8,18]
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function NeedViewRelease($cart_ids = '')
    {
        /*初始化*/
        $manage_u = session('ManageUser');
        $cart = new Cart();
        $product = new Product();
        $where = array();
        $this->ViewData['cart_order'] = array();
        $this->ViewData['product_category'] = $product->getProductCategoryList(array(), array(['product_category.sort', 'desc']), false);

        $cart_id_arr = collect(explode(',', $cart_ids))->filter()->toArray();

        if (!empty($cart_id_arr))
        {
            $rules = [
                '*' => [
                    Rule::exists('shopping_cart', 'cart_id')->where(function ($query) use ($manage_u)
                    {
                        $query->where('user_id', $manage_u->user_id);
                    }),
                ]
            ];
            $validator = Validator::make(array('cart_id_arr' => $cart_id_arr), $rules);
            if ($validator->passes())
            {
                /*加入sql条件购物车所有者id*/
                array_push($where, ['shopping_cart.user_id', '=', $manage_u->user_id]);
                $this->ViewData['cart_order'] = $cart->getCartList($where, array(['shopping_cart.create_time', 'desc']), false)->whereIn('cart_id', $cart_id_arr);
            }
        }
        return view('platform_need_release', $this->ViewData);
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
        $this->ViewData['order_info'] = $order_info;
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
     * View 平台订单详情 页面
     * @param $order_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function OrderDetailView($order_id)
    {
        /*初始化*/
        $platform = new Platform();

        $this->ViewData['order_info'] = $platform->getOrderInfo($order_id);
        $this->ViewData['log_list'] = $platform->getOrderLog($order_id);

        return view('platform_order_detail_view', $this->ViewData);
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
//        $arr = array(
//            'order_id' => 342,
//            'confirm_time' => '2018-3-3',
//            'platform_receive_time' => '2018-3-3',
//            'supplier_A_id' => '12',
//            'supplier_A_number' => '200',
////            'supplier_B_id' => '3',
////            'supplier_B_number' => null,
////            'platform_allocation_number' => '100',
//            'warning_time' => '0'
//        );
//        $request->merge($arr);
        /*初始化*/
        $m3result = new M3Result();
        $platform = new Platform();
        $order_info = $platform->getOrderInfo($request->input('order_id'));

        /*验证规则*/
        $rules = [
            'warning_time' => 'required|integer',
            'supplier_A_number' => 'nullable|numeric',
            'supplier_B_number' => 'nullable|numeric',
            'supplier_C_number' => 'nullable|numeric',
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
        $validator->sometimes('platform_receive_time', ['date', 'after:now', 'before:' . $order_info->army_receive_date], function ($input) use ($order_info)
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
            $residue_number = bcsub($order_info->product_number, collect($supplier_arr)->sum('product_number'), 2);/*减去供货商供应量*/
            if (!$product_info || $residue_number != 0.00)
            {
                $m3result->code = 2;
                $m3result->messages = '分配数量不正确或产品不存在';
                return $m3result->toJson();
            }
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
//        $arr = array(
//            'order_id' => 7,
//            'confirm_time' => '2018-2-3',
//            'platform_receive_time' => '2018-2-3',
//            'supplier_A_id' => '5',
//            'supplier_A_number' => '50',
//            'supplier_B_id' => '3',
//            'supplier_B_number' => '50',
//
//            'platform_allocation_number' => '300',
//            'warning_time' => '0'
//        );
//        $request->merge($arr);
        /*初始化*/
        $m3result = new M3Result();
        $platform = new Platform();
        $order_info = $platform->getOrderInfo($request->input('order_id'));

        /*验证规则*/
        $rules = [
            'warning_time' => 'required|integer',
            'supplier_A_number' => 'nullable|numeric',
            'supplier_B_number' => 'nullable|numeric',
            'supplier_C_number' => 'nullable|numeric',
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
        $validator->sometimes('platform_receive_time', ['date', 'after:now', 'before:' . $order_info->army_receive_date], function ($input) use ($order_info)
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
            $confirm_offer = $order_info->offer_info->filter(function ($value)
            {
                return $value->status == CommonModel::OFFER_AWAIT_CONFIRM;
            });
            $residue_number = bcsub($order_info->product_number, $confirm_offer->sum('product_number'), 2);/*减去已经确认的报价*/
            $residue_number = bcsub($residue_number, collect($supplier_arr)->sum('product_number'), 2);/*减去供货商供应量*/
            if (!$product_info || $residue_number != 0.00)
            {
                $m3result->code = 2;
                $m3result->messages = '分配数量不正确或产品不存在';
                return $m3result->toJson();
            }
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
//        $arr = array(
//            'order_id' => '7',
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
                if (bcsub($repertory_info['number'], $order_info->platform_allocation_number, 2) < 0)
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

        if ($validator->passes())
        {
            $order_info = $platform->getOrderInfo($request->input('order_id'));
            $repertory_info = $platform->getRepertory($order_info->product_name, $order_info->spec_name);
            if (bcsub($repertory_info['number'], $order_info->product_number, 2) < 0)
            {
                $m3result->code = 2;
                $m3result->messages = '库存供应数量不足';
            }
            else
            {
                $platform->inventorySupplyNeed($request->input('order_id'));
                $m3result->code = 0;
                $m3result->messages = '平台库存供应';
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
     * Ajax 平台确认收货 请求处理
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
            'order_json' => 'required|json'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && is_array($order_json_arr = json_decode($request->input('order_json'), true)))
        {
            /*验证json数据有效性*/
            $json_rules = [
                '*.product_number' => 'required|numeric|min:0.01',
                '*.product_name' => 'required',
                '*.spec_name' => 'required',
            ];
            $validator_json = Validator::make($order_json_arr, $json_rules);

            if ($validator_json->passes())
            {   /*验证通过*/

                /*事物*/
                try
                {
                    DB::transaction(function () use ($order_json_arr, $platform, $m3result)
                    {
                        foreach ($order_json_arr as $item)
                        {
                            if (!$platform->releaseNeed($item))
                                throw new \Exception('Transaction Exception');
                        }
                        $m3result->code = 0;
                        $m3result->messages = '平台需求发布成功';
                    });

                } catch (\Exception $e)
                {
                    $m3result->code = 3;
                    $m3result->data['platform'] = $platform->messages();
                    $m3result->data['validator'] = $validator_json->messages();
                    $m3result->messages = $m3result->data['platform']['messages'];
                }

            }
            else
            {
                $m3result->code = 2;
                $m3result->messages = '数据验证失败';
                $m3result->data['validator'] = $validator_json->messages();
            }
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '请传入Json数据';
            $m3result->data['validator'] = $validator->messages();
        }

        return $m3result->toJson();
    }

    /**
     * 平台 统计导出Excel
     * @param $start_date
     * @param $end_date
     * @return \Illuminate\Http\RedirectResponse
     */
    public function StatisticsOutputExcel($start_date, $end_date)
    {
        /*初始化*/
        $time_where = array();

        if (strtotime($start_date) && strtotime($end_date))
        {
            $start_dt = Carbon::parse($start_date);
            $end_dt = Carbon::parse($end_date);
            array_push($time_where, ['order_offer.create_time', '>=', $start_dt->timestamp]);
            array_push($time_where, ['order_offer.create_time', '<=', $end_dt->timestamp]);

            $offer_list = OrderOffer::where($time_where)->where('status', CommonModel::OFFER_ALREADY_RECEIVE)->orderBy('create_time', 'desc')->with('ho_users', 'ho_orders', 'ho_orders.ho_users')->get();

            $cellData = [
                ['平台统计打印信息'],
                ['导出时间:' . now()->toDateTimeString()],
                ['供应商名称', '军方名称', '订单编号', '货品名称', '货品规格', '军方单价', '供应商单价', '货品数量', '军方总价', '供应商总价', '订单创建时间']
            ];
            Excel::create('平台统计打印信息' . now()->toDateString(), function ($excel) use ($cellData, $offer_list)
            {
                /*全局样式*/
                $excel->getDefaultStyle()
                    ->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

                $excel->sheet('列表一', function ($sheet) use ($cellData, $offer_list)
                {
                    $sheet->rows($cellData);

                    /*标题样式*/
                    $sheet->mergeCells('A1:K1');
                    $sheet->cells('A1', function ($cells)
                    {
                        $cells->setFontColor('#ff2832');
                        $cells->setFontSize(16);
                        $cells->setFontWeight('bold');
                    });
                    $sheet->setHeight(1, 40);

                    /*导出时间样式*/
                    $sheet->mergeCells('A2:K2');
                    $sheet->cells('A2:K2', function ($cells)
                    {
                        $cells->setAlignment('left');
                        $cells->setFontColor('#548235');
                        $cells->setFontSize(12);
                        $cells->setFontWeight('bold');
                    });
                    $sheet->setHeight(2, 30);

                    /*表头样式*/
                    $sheet->setBorder('A3:K3', 'thin');
                    $sheet->cells('A3:K3', function ($cells)
                    {
                        $cells->setFontSize(12);
                        $cells->setFontWeight('bold');
                    });
                    $sheet->setHeight(3, 30);

                    /*宽度设置*/
                    $sheet->setWidth('A', 30);
                    $sheet->setWidth('B', 30);
                    $sheet->setWidth('C', 20);
                    $sheet->setWidth('D', 20);
                    $sheet->setWidth('E', 20);
                    $sheet->setWidth('F', 15);
                    $sheet->setWidth('G', 15);
                    $sheet->setWidth('H', 15);
                    $sheet->setWidth('I', 15);
                    $sheet->setWidth('J', 15);
                    $sheet->setWidth('K', 25);

                    /*循环加入数据*/
                    $supplier = new Supplier();
                    $product = new Product();
                    $start_index = 4;
                    $offer_list->each(function ($item) use ($product, $supplier, $sheet, &$start_index)
                    {
                        $item->total_price = bcmul($item->price, $item->product_number, 2);
                        $e_products = $product->checkProduct($item->ho_orders->product_name, $item->ho_orders->spec_name);
                        $e_products ? $item->army_price = $e_products->spec_info->product_price : $item->army_price = 0;
                        $item->army_total_price = bcmul($item->army_price, $item->product_number, 2);
                        $sheet->appendRow(array(
                            $item->ho_users->nick_name,
                            $item->ho_orders->ho_users->nick_name,
                            $item->ho_orders->order_sn,
                            $item->ho_orders->product_name,
                            $item->ho_orders->spec_name,
                            $item->army_price . '元',
                            $item->price . '元',
                            $item->product_number . $item->ho_orders->spec_unit,
                            $item->army_total_price . '元',
                            $item->total_price . '元',
                            Carbon::createFromTimestamp($item->create_time)->toDateTimeString(),
                        ));
                        $sheet->setBorder('A' . $start_index . ':K' . $start_index, 'thin');
                        $sheet->setHeight($start_index, 20);
                        $start_index++;
                    });

                });
            })->export('xls');
        }
        return back();
    }

    /**
     * 平台 导出Excel
     * @param $start_date
     * @param $end_date
     * @return \Illuminate\Http\RedirectResponse
     */
    public function OutputExcel($start_date, $end_date)
    {
        /*初始化*/
        $time_where = array();

        if (strtotime($start_date) && strtotime($end_date))
        {
            $start_dt = Carbon::parse($start_date);
            $end_dt = Carbon::parse($end_date);
            array_push($time_where, ['order_offer.create_time', '>=', $start_dt->timestamp]);
            array_push($time_where, ['order_offer.create_time', '<=', $end_dt->timestamp]);

            $offer_list = OrderOffer::where($time_where)->orderBy('offer_id', 'asc')->with('ho_orders', 'ho_users')->get();

            $cellData = [
                ['平台打印信息'],
                ['导出时间:' . now()->toDateTimeString()],
                ['序号', '订单编号', '货品名称', '军方下单时间', '军方规定到货时间', '供应商名称', '货品状态', '货品质检状态', '军方联系人', '联系电话', '货品数量', '货品规格', '货品单价', '货品总价'],
            ];
            Excel::create('平台打印信息' . now()->toDateString(), function ($excel) use ($cellData, $offer_list)
            {
                /*全局样式*/
                $excel->getDefaultStyle()
                    ->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

                $excel->sheet('列表一', function ($sheet) use ($cellData, $offer_list)
                {
                    $sheet->rows($cellData);

                    /*标题样式*/
                    $sheet->mergeCells('A1:N1');
                    $sheet->cells('A1', function ($cells)
                    {
                        $cells->setFontColor('#ff2832');
                        $cells->setFontSize(16);
                        $cells->setFontWeight('bold');
                    });
                    $sheet->setHeight(1, 40);

                    /*导出时间样式*/
                    $sheet->mergeCells('A2:N2');
                    $sheet->cells('A2:N2', function ($cells)
                    {
                        $cells->setAlignment('left');
                        $cells->setFontColor('#548235');
                        $cells->setFontSize(12);
                        $cells->setFontWeight('bold');
                    });
                    $sheet->setHeight(2, 30);

                    /*表头样式*/
                    $sheet->setBorder('A3:N3', 'thin');
                    $sheet->cells('A3:N3', function ($cells)
                    {
                        $cells->setFontSize(12);
                        $cells->setFontWeight('bold');
                    });
                    $sheet->setHeight(3, 30);

                    /*宽度设置*/
                    $sheet->setWidth('A', 10);
                    $sheet->setWidth('B', 30);
                    $sheet->setWidth('C', 20);
                    $sheet->setWidth('D', 20);
                    $sheet->setWidth('E', 20);
                    $sheet->setWidth('F', 20);
                    $sheet->setWidth('G', 15);
                    $sheet->setWidth('H', 15);
                    $sheet->setWidth('I', 15);
                    $sheet->setWidth('J', 15);
                    $sheet->setWidth('K', 15);
                    $sheet->setWidth('L', 20);
                    $sheet->setWidth('M', 15);
                    $sheet->setWidth('N', 15);

                    /*循环加入数据*/
                    $supplier = new Supplier();
                    $total_price = 0;
                    $start_index = 4;
                    $offer_list->each(function ($item) use (&$total_price, $supplier, $sheet, &$start_index)
                    {
                        $item->total_price = bcmul($item->price, $item->product_number, 2);
                        $sheet->appendRow(array(
                            $item->offer_id,
                            $item->ho_orders->order_sn,
                            $item->ho_orders->product_name,
                            Carbon::createFromTimestamp($item->ho_orders->create_time)->toDateTimeString(),
                            Carbon::createFromTimestamp($item->ho_orders->army_receive_time)->toDateTimeString(),
                            $item->ho_users->nick_name,
                            $supplier->offerStatusTransformText($item->status),
                            $supplier->orderQualityCheckTransformText($item->quality_check),
                            $item->ho_orders->army_contact_person,
                            $item->ho_orders->army_contact_tel,
                            $item->product_number . $item->ho_orders->spec_unit,
                            $item->ho_orders->spec_name,
                            $item->price . '元',
                            $item->total_price . '元'
                        ));
                        $sheet->setBorder('A' . $start_index . ':N' . $start_index, 'thin');
                        $sheet->setHeight($start_index, 20);
                        $start_index++;
                        $total_price = bcadd($total_price, $item->total_price, 2);
                    });

                    /*总价*/
                    $total_price_index = ++$start_index;
                    $sheet->appendRow($total_price_index, array(
                        '货品总额:' . $total_price . '元'
                    ));
                    $sheet->mergeCells('A' . $total_price_index . ':N' . $total_price_index);
                    $sheet->cells('A' . $total_price_index . ':N' . $total_price_index, function ($cells)
                    {
                        $cells->setAlignment('right');
                        $cells->setFontWeight('bold');
                    });
                    $sheet->setHeight($total_price_index, 30);

                });
            })->export('xls');
        }
        return back();
    }

    /**
     * Ajax 平台打印 请求数据
     * @param Request $request
     * @return \App\Tools\json
     */
    public function OutputPrint(Request $request)
    {
        /*初始化*/
        $m3result = new M3Result();
        $product = new Product();
        $platform = new Platform();

        $order_id_arr = collect(explode(',', $request->input('order_ids')))->filter()->toArray();

        if (!empty($order_id_arr))
        {
            $rules = [
                '*' => 'exists:orders,order_id',
            ];
            $validator = Validator::make(array('order_id_arr' => $order_id_arr), $rules);
            if ($validator->passes())
            {
                $list = Orders::whereIn('order_id', $order_id_arr)->get();
                $list->transform(function ($item) use ($product, $platform)
                {
                    $e_products = $product->checkProduct($item->product_name, $item->spec_name);
                    $e_products ? $item->price = $e_products->spec_info->product_price : $item->price = 0;
                    $item->total_price = bcmul($item->price, $item->product_number, 2);
                    $item->create_date = Carbon::createFromTimestamp($item->create_time)->toDateTimeString();
                    $item->army_receive_date = $item->type == $platform::ORDER_TYPE_PLATFORM ? '' : Carbon::createFromTimestamp($item->army_receive_time)->toDateTimeString();
                    $item->quality_check_text = $platform->orderQualityCheckTransformText($item->quality_check);
                    $item->status_text = $platform->orderStatusTransformText($item->type, $item->status);
                    return $item;
                });
                $m3result->code = 0;
                $m3result->messages = '获取平台打印信息';
                $m3result->data = $list;
                return $m3result->toJson();
            }
        }
        $m3result->code = 1;
        $m3result->messages = '数据验证失败';
        return $m3result->toJson();
    }

    /**
     * Ajax 统计打印 请求数据
     * @param Request $request
     * @return \App\Tools\json
     */
    public function StatisticsOutputPrint(Request $request)
    {
//        $arr = array(
//            'offer_ids' => '22',
//        );
//        $request->merge($arr);

        /*初始化*/
        $m3result = new M3Result();
        $product = new Product();
        $supplier = new Supplier();

        $offer_id_arr = collect(explode(',', $request->input('offer_ids')))->filter()->toArray();

        if (!empty($offer_id_arr))
        {
            $rules = [
                '*' => 'exists:order_offer,offer_id',
            ];
            $validator = Validator::make(array('offer_id_arr' => $offer_id_arr), $rules);
            if ($validator->passes())
            {
                $list = OrderOffer::whereIn('offer_id', $offer_id_arr)->with('ho_orders', 'ho_users')->get();
                $list->transform(function ($item) use ($product, $supplier)
                {
                    $item->order_info = $item->ho_orders;
                    $item->user_info = $item->ho_users;
                    $item->total_price = bcmul($item->price, $item->product_number, 2);
                    $item->platform_receive_date = Carbon::createFromTimestamp($item->platform_receive_time)->toDateTimeString();
                    $item->status_text = $supplier->offerStatusTransformText($item->status);
                    $item->create_date = Carbon::createFromTimestamp($item->create_time)->toDateTimeString();
                    unset($item->ho_orders);
                    unset($item->ho_users);
                    return $item;
                });
                $m3result->code = 0;
                $m3result->messages = '获取统计打印信息';
                $m3result->data = $list;
                return $m3result->toJson();
            }
        }
        $m3result->code = 1;
        $m3result->messages = '数据验证失败';
        return $m3result->toJson();
    }

}