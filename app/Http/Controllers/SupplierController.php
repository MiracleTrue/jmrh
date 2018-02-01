<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */
namespace App\Http\Controllers;

use App\Entity\OrderOffer;
use App\Models\Army;
use App\Models\CommonModel;
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
 * 供应商控制器
 * Class SupplierController
 * @package App\Http\Controllers
 */
class SupplierController extends Controller
{
    public $ViewData = array(); /*传递页面的数组*/

    /**
     * View 供应商报价列表 页面 (搜索条件参数: 报价状态, 创建时间)
     * @param string $status
     * @param string $create_time
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function NeedList($status = 'null', $create_time = 'null')
    {
        /*初始化*/
        $supplier = new Supplier();
        $manage_u = session('ManageUser');
        $where = array();
        $this->ViewData['offer_list'] = array();

        /*加入sql条件供货商id*/
        if ($manage_u->identity == User::SUPPLIER_ADMIN)
        {
            array_push($where, ['order_offer.user_id', '=', $manage_u->user_id]);
        }

        /*条件搜索*/
        switch ($status)
        {
            case '待回复' :
                array_push($where, ['order_offer.status', '=', $supplier::OFFER_AWAIT_REPLY]);
                break;
            case '待确认':
                array_push($where, ['order_offer.status', '=', $supplier::OFFER_AWAIT_CONFIRM]);
                break;
            case '待发货' :
                array_push($where, ['order_offer.status', '=', $supplier::OFFER_AWAIT_SEND]);
                break;
            case '已发货' :
                array_push($where, ['order_offer.status', '=', $supplier::OFFER_ALREADY_SEND]);
                break;
            case '已收货':
                array_push($where, ['order_offer.status', '=', $supplier::OFFER_ALREADY_RECEIVE]);
                break;
            case '已拒绝':
                array_push($where, ['order_offer.status', '=', $supplier::OFFER_ALREADY_DENY]);
                break;
            case '已过期' :
                array_push($where, ['order_offer.status', '=', $supplier::OFFER_OVERDUE]);
                break;
        }
        if (!empty($create_time) && strtotime($create_time))
        {
            $dt = Carbon::parse($create_time);
            $start_dt = Carbon::create($dt->year, $dt->month, $dt->day, 0, 0, 0)->timestamp;
            $end_dt = Carbon::create($dt->year, $dt->month, $dt->day, 0, 0, 0)->addDay()->subSecond()->timestamp;
            array_push($where, ['order_offer.create_time', '>=', $start_dt]);
            array_push($where, ['order_offer.create_time', '<=', $end_dt]);
        }

        $this->ViewData['manage_user'] = $manage_u;
        $this->ViewData['offer_list'] = $supplier->getOfferList($where);
        $this->ViewData['page_search'] = array('status' => $status, 'create_time' => $create_time);

//        dump($this->ViewData);
        return view('supplier_need_list', $this->ViewData);
    }

    /**
     * View 供应商报价 页面
     * @param $offer_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function OfferView($offer_id)
    {
        /*初始化*/
        $supplier = new Supplier();
        $manage_u = session('ManageUser');
        /*验证规则*/
        $rules = [
            'offer_id' => [
                'required',
                'integer',
                Rule::exists('order_offer')->where(function ($query) use ($offer_id, $manage_u)
                {
                    $query->where('offer_id', $offer_id)->where('user_id', $manage_u->user_id)->where('status', CommonModel::OFFER_AWAIT_REPLY)->where('confirm_time', '>=', now()->timestamp);
                }),
            ]
        ];
        $validator = Validator::make(array('offer_id' => $offer_id), $rules);

        if ($validator->passes() || $manage_u->identity = User::ADMINISTRATOR)
        {   /*验证通过*/
            $this->ViewData['offer_info'] = $supplier->getSupplierOfferInfo($offer_id);
        }
        else
        {
            return CommonModel::noPrivilegePrompt(request());/*没有权限*/
        }
//        dump($this->ViewData);
        return view('supplier_offer_view', $this->ViewData);
    }

    /**
     * Ajax 供应商同意供货 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function OfferSubmit(Request $request)
    {
//        $arr = array(
//            'offer_id' => '4',
//        );
//        $request->merge($arr);
        /*初始化*/
        $supplier = new Supplier();
        $m3result = new M3Result();
        $manage_u = session('ManageUser');

        /*验证规则*/
        $rules = [
            'offer_id' => [
                'required',
                'integer',
                Rule::exists('order_offer')->where(function ($query) use ($request, $manage_u)
                {
                    $query->where('offer_id', $request->input('offer_id'))->where('user_id', $manage_u->user_id)
                        ->where('status', CommonModel::OFFER_AWAIT_REPLY)->where('confirm_time', '>=', now()->timestamp);
                }),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $supplier->supplierSubmitOffer($manage_u->user_id, $request->input('offer_id')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '同意供货';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['supplier'] = $supplier->messages();
        }
        return $m3result->toJson();
    }

    /**
     * Ajax 供应商拒绝供货 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function OfferDeny(Request $request)
    {
//        $arr = array(
//            'offer_id' => '4',
//            'deny_reason' => '没货',
//        );
//        $request->merge($arr);
        /*初始化*/
        $supplier = new Supplier();
        $m3result = new M3Result();
        $manage_u = session('ManageUser');

        /*验证规则*/
        $rules = [
            'offer_id' => [
                'required',
                'integer',
                Rule::exists('order_offer')->where(function ($query) use ($request, $manage_u)
                {
                    $query->where('offer_id', $request->input('offer_id'))->where('user_id', $manage_u->user_id)
                        ->where('status', CommonModel::OFFER_AWAIT_REPLY)->where('confirm_time', '>=', now()->timestamp);
                }),
            ],
            'deny_reason' => 'nullable|string'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $supplier->supplierDenyOffer($manage_u->user_id, $request->input('offer_id'), $request->input('deny_reason')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '拒绝供货';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['supplier'] = $supplier->messages();
        }
        return $m3result->toJson();
    }

    /**
     * Ajax 供应商配货 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function SendProduct(Request $request)
    {
//        $arr = array(
//            'offer_id' => '4',
//        );
//        $request->merge($arr);
        /*初始化*/
        $supplier = new Supplier();
        $m3result = new M3Result();
        $manage_u = session('ManageUser');

        /*验证规则*/
        $rules = [
            'offer_id' => [
                'required',
                'integer',
                Rule::exists('order_offer')->where(function ($query) use ($request, $manage_u)
                {
                    $query->where('offer_id', $request->input('offer_id'))->where('user_id', $manage_u->user_id)
                        ->where('status', CommonModel::OFFER_AWAIT_SEND);
                }),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $supplier->supplierSendProduct($manage_u->user_id, $request->input('offer_id')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '配货成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['supplier'] = $supplier->messages();
        }
        return $m3result->toJson();
    }

    /**
     * 供应商 导出Excel
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
            array_push($time_where, ['order_offer.create_time', '>=', $start_dt->timestamp]);
            array_push($time_where, ['order_offer.create_time', '<=', $end_dt->timestamp]);

            $offer_list = OrderOffer::where($time_where)->where('user_id', $manage_u->user_id)->orderBy('offer_id', 'asc')->with('ho_orders')->get();

            $cellData = [
                ['供应商打印信息'],
                ['导出时间:' . now()->toDateTimeString()],
                ['序号', '订单编号', '下单时间', '平台规定到货时间', '货品名称', '货品单价', '货品数量', '货品规格', '货品总价', '货品状态']
            ];
            Excel::create('供应商打印信息' . now()->toDateString(), function ($excel) use ($cellData, $offer_list)
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
                    $sheet->mergeCells('A1:J1');
                    $sheet->cells('A1', function ($cells)
                    {
                        $cells->setFontColor('#ff2832');
                        $cells->setFontSize(16);
                        $cells->setFontWeight('bold');
                    });
                    $sheet->setHeight(1, 40);

                    /*导出时间样式*/
                    $sheet->mergeCells('A2:J2');
                    $sheet->cells('A2:J2', function ($cells)
                    {
                        $cells->setAlignment('left');
                        $cells->setFontColor('#548235');
                        $cells->setFontSize(12);
                        $cells->setFontWeight('bold');
                    });
                    $sheet->setHeight(2, 30);

                    /*表头样式*/
                    $sheet->setBorder('A3:J3', 'thin');
                    $sheet->cells('A3:J3', function ($cells)
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
                            Carbon::createFromTimestamp($item->create_time)->toDateTimeString(),
                            Carbon::createFromTimestamp($item->platform_receive_time)->toDateTimeString(),
                            $item->ho_orders->product_name,
                            $item->price . '元',
                            $item->product_number . $item->ho_orders->spec_unit,
                            $item->ho_orders->spec_name,
                            $item->total_price . '元',
                            $supplier->offerStatusTransformText($item->status),
                        ));
                        $sheet->setBorder('A' . $start_index . ':J' . $start_index, 'thin');
                        $sheet->setHeight($start_index, 20);
                        $start_index++;
                        $total_price = bcadd($total_price, $item->total_price, 2);
                    });

                    /*总价*/
                    $total_price_index = ++$start_index;
                    $sheet->appendRow($total_price_index, array(
                        '货品总额:' . $total_price . '元'
                    ));
                    $sheet->mergeCells('A' . $total_price_index . ':J' . $total_price_index);
                    $sheet->cells('A' . $total_price_index . ':J' . $total_price_index, function ($cells)
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