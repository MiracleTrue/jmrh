<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */
namespace App\Http\Controllers;

use App\Models\Army;
use App\Models\Cart;
use App\Models\CommonModel;
use App\Models\Product;
use App\Models\User;
use App\Tools\M3Result;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


/**
 * 军方控制器
 * Class ArmyController
 * @package App\Http\Controllers
 */
class ArmyController extends Controller
{
    public $ViewData = array(); /*传递页面的数组*/

    /**
     * View 军方订单列表 页面 (搜索条件参数: 订单状态, 创建时间)
     * @param string $status
     * @param string $create_time
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function NeedList($status = 'null', $create_time = 'null')
    {
        /*初始化*/
        $manage_u = session('ManageUser');
        $army = new Army();
        $where = array();
        $this->ViewData['order_list'] = array();
        $this->ViewData['manage_user'] = $manage_u;

        /*加入sql条件军方id*/
        if ($manage_u->identity == User::ARMY_ADMIN)
        {
            array_push($where, ['orders.army_id', '=', $manage_u->user_id]);
        }

        /*条件搜索*/
        switch ($status)
        {
            case '待确认' :
                array_push($where, ['orders.status', '=', $army::ORDER_AWAIT_ALLOCATION]);
                break;
            case '已确认':
                array_push($where, ['orders.status', '!=', $army::ORDER_AWAIT_ALLOCATION]);
                array_push($where, ['orders.status', '!=', $army::ORDER_SEND_ARMY]);
                array_push($where, ['orders.status', '!=', $army::ORDER_SUCCESSFUL]);
                break;
            case '已发货' :
                array_push($where, ['orders.status', '=', $army::ORDER_SEND_ARMY]);
                break;
            case '已到货' :
                array_push($where, ['orders.status', '=', $army::ORDER_SUCCESSFUL]);
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
        $this->ViewData['order_list'] = $army->getOrderList($where);
        $this->ViewData['page_search'] = array('status' => $status, 'create_time' => $create_time);
//        dump($this->ViewData);
        return view('army_need_list', $this->ViewData);
    }

    /**
     * View 军方需求发布 页面
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
                    Rule::exists('shopping_cart','cart_id')->where(function ($query) use ($manage_u)
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
                $this->ViewData['cart_order'] = $cart->getCartList($where,array(['shopping_cart.create_time', 'desc']),false)->whereIn('cart_id',$cart_id_arr);
            }
        }
        return view('army_need_release', $this->ViewData);
    }

    /**
     * View 军方需求编辑 页面
     * @param $order_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function NeedViewEdit($order_id)
    {
        /*初始化*/
        $manage_u = session('ManageUser');
        $army = new Army();
        $this->ViewData['order_info'] = array();
        /*验证规则*/
        $rules = [
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders')->where(function ($query) use ($order_id, $manage_u)
                {
                    $query->where('order_id', $order_id)->where('army_id', $manage_u->user_id)
                        ->where('status', CommonModel::ORDER_AWAIT_ALLOCATION)
                        ->where('type', Army::ORDER_TYPE_ARMY);
                }),
            ]
        ];
        $validator = Validator::make(array('order_id' => $order_id), $rules);

        if ($validator->passes() || $manage_u->identity = User::ADMINISTRATOR)
        {   /*验证通过*/
            $this->ViewData['order_info'] = $army->getOrderInfo($order_id);
        }
        else
        {
            return CommonModel::noPrivilegePrompt(request());/*没有权限*/
        }
        return view('army_need_edit', $this->ViewData);
    }

    /**
     * Ajax 军方发布需求 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function NeedRelease(Request $request)
    {
        /*初始化*/
        $army = new Army();
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
                '*.army_receive_time' => 'required|date|after:now',
                '*.army_contact_person' => 'sometimes|max:45',
                '*.army_contact_tel' => 'sometimes|max:15',
                '*.army_note' => 'sometimes|max:255',
                '*.product_name' => 'required',
                '*.spec_name' => 'required',
            ];
            $validator_json = Validator::make($order_json_arr, $json_rules);

            if ($validator_json->passes())
            {   /*验证通过*/

                /*事物*/
                try
                {
                    DB::transaction(function () use ($order_json_arr, $army, $m3result)
                    {
                        foreach ($order_json_arr as $item)
                        {
                            if (!$army->releaseNeed($item))
                                throw new \Exception('Transaction Exception');
                        }
                        $m3result->code = 0;
                        $m3result->messages = '军方需求发布成功';
                    });

                } catch (\Exception $e)
                {
                    $m3result->code = 3;
                    $m3result->data['army'] = $army->messages();
                    $m3result->data['validator'] = $validator_json->messages();
                    $m3result->messages = $m3result->data['army']['messages'];
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
     * Ajax 修改军方需求 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function NeedEdit(Request $request)
    {
        /*初始化*/
        $manage_u = session('ManageUser');
        $army = new Army();
        $m3result = new M3Result();
        /*验证规则*/
        $rules = [
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders')->where(function ($query) use ($manage_u)
                {
                    $query->where('order_id', $GLOBALS['request']->input('order_id'))->where('army_id', $manage_u->user_id)
                        ->where('status', CommonModel::ORDER_AWAIT_ALLOCATION)
                        ->where('type', Army::ORDER_TYPE_ARMY);
                }),
            ],
            'product_number' => 'required|numeric|min:0.01',
            'army_receive_time' => 'required|date|after:now',
            'army_contact_person' => 'sometimes|max:45',
            'army_contact_tel' => 'sometimes|max:15',
            'army_note' => 'sometimes|max:255',
            'product_name' => 'required',
            'spec_name' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $army->editNeed($request->all()))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '军方需求修改成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['army'] = $army->messages();
            if ($m3result->data['army']['code'] == 1)
            {
                $m3result->code = 2;
                $m3result->messages = $m3result->data['army']['messages'];
            }
        }

        return $m3result->toJson();
    }

    /**
     * Ajax 军方确认收货 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ConfirmReceive(Request $request)
    {
        /*初始化*/
        $manage_u = session('ManageUser');
        $m3result = new M3Result();
        $army = new Army();

        /*验证规则*/
        $rules = [
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders')->where(function ($query) use ($manage_u)
                {
                    $query->where('order_id', $GLOBALS['request']->input('order_id'))->where('army_id', $manage_u->user_id)
                        ->where('status', CommonModel::ORDER_SEND_ARMY);
                }),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $army->armyConfirmReceive($request->input('order_id')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '确认收货成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['army'] = $army->messages();
        }
        return $m3result->toJson();
    }

    /**
     * Ajax 删除军方需求 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function NeedDelete(Request $request)
    {
        /*初始化*/
        $manage_u = session('ManageUser');
        $army = new Army();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders')->where(function ($query) use ($manage_u)
                {
                    $query->where('order_id', $GLOBALS['request']->input('order_id'))->where('army_id', $manage_u->user_id)
                        ->where('type', Army::ORDER_TYPE_ARMY)
                        ->where('status', CommonModel::ORDER_AWAIT_ALLOCATION);
                }),
            ],
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $army->deleteNeed($request->input('order_id')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '军方需求删除成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['army'] = $army->messages();
        }

        return $m3result->toJson();
    }

}