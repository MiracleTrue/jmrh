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
use Maatwebsite\Excel\Facades\Excel;


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
        $product = new Product();
        $this->ViewData['order_info'] = array();
        $this->ViewData['product_category'] = $product->getProductCategoryList(array(), array(['product_category.sort', 'desc']), false);
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

    /**
     * 军方 导出Excel
     * @param $start_date
     * @param $end_date
     * @return \Illuminate\Http\RedirectResponse
     */
    public function OutputExcel($start_date, $end_date)
    {
        /*初始化*/
        $time_where = array();
        $manage_u = session('ManageUser');

        if (strtotime($start_date) && strtotime($end_date))
        {
            $start_dt = Carbon::parse($start_date);
            $end_dt = Carbon::parse($end_date);
            array_push($time_where, ['create_time', '>=', $start_dt->timestamp]);
            array_push($time_where, ['create_time', '<=', $end_dt->timestamp]);

            $order_list = Orders::where('army_id', $manage_u->user_id)->where($time_where)->orderBy('order_id', 'asc')->get();

            $cellData = [
                ['军方打印信息'],
                ['导出时间:' . now()->toDateTimeString()],
                ['序号', '订单编号', '货品名称', '下单时间', '到货时间', '货品状态', '货品质检状态', '货品数量', '货品规格', '货品单价', '货品总价'],
            ];
            Excel::create('军方打印信息' . now()->toDateString(), function ($excel) use ($cellData, $order_list)
            {
                /*全局样式*/
                $excel->getDefaultStyle()
                    ->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

                $excel->sheet('列表一', function ($sheet) use ($cellData, $order_list)
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

                    /*循环加入数据*/
                    $army = new Army();
                    $product = new Product();
                    $total_price = 0;
                    $start_index = 4;
                    $order_list->each(function ($item) use (&$total_price, $army, $product, $sheet, &$start_index)
                    {
                        $e_products = $product->checkProduct($item->product_name, $item->spec_name);
                        $e_products ? $item->price = $e_products->spec_info->product_price : $item->price = 0;
                        $item->total_price = bcmul($item->price, $item->product_number, 2);
                        $sheet->appendRow(array(
                            $item->order_id,
                            $item->order_sn,
                            $item->product_name,
                            Carbon::createFromTimestamp($item->create_time)->toDateTimeString(),
                            Carbon::createFromTimestamp($item->army_receive_time)->toDateTimeString(),
                            $army->orderStatusTransformText($item->status),
                            $army->orderQualityCheckTransformText($item->quality_check),
                            $item->product_number . $item->spec_unit,
                            $item->spec_name,
                            $item->price . '元',
                            $item->total_price . '元'
                        ));
                        $sheet->setBorder('A' . $start_index . ':K' . $start_index, 'thin');
                        $sheet->setHeight($start_index, 20);
                        $start_index++;
                        $total_price = bcadd($total_price, $item->total_price, 2);
                    });

                    /*总价*/
                    $total_price_index = ++$start_index;
                    $sheet->appendRow($total_price_index, array(
                        '货品总额:' . $total_price . '元'
                    ));
                    $sheet->mergeCells('A' . $total_price_index . ':K' . $total_price_index);
                    $sheet->cells('A' . $total_price_index . ':K' . $total_price_index, function ($cells)
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


}