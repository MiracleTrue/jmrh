<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */
namespace App\Http\Controllers;

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


/**
 * 供应商控制器
 * Class SupplierController
 * @package App\Http\Controllers
 */
class SupplierController extends Controller
{
    public $ViewData = array(); /*传递页面的数组*/

    /**
     * View 平台订单列表 页面 (搜索条件参数: 订单状态, 创建时间)
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
        $or_where = array();
        $this->ViewData['offer_list'] = array();

        /*加入sql条件供货商id*/
        if ($manage_u->identity == User::SUPPLIER_ADMIN)
        {
            array_push($where, ['order_offer.user_id', '=', $manage_u->user_id]);
        }

        /*条件搜索*/
        switch ($status)
        {
            case '待报价' :
                array_push($where, ['order_offer.status', '=', $supplier::OFFER_AWAIT_OFFER]);
                break;
            case '等待确认':
                array_push($where, ['order_offer.status', '=', $supplier::OFFER_AWAIT_PASS]);
                break;
            case '待发货' :
                array_push($where, ['order_offer.status', '=', $supplier::OFFER_PASSED]);
                break;
            case '已发货' :
                array_push($where, ['order_offer.status', '=', $supplier::OFFER_SEND]);
                break;
            case '未通过':
                array_push($where, ['order_offer.status', '=', $supplier::OFFER_NOT_PASS]);
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
        $this->ViewData['offer_list'] = $supplier->getOfferList($where, $or_where);
        $this->ViewData['page_search'] = array('status' => $status, 'create_time' => $create_time);

        dump($this->ViewData);
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
                    $query->where('offer_id', $offer_id)->where('user_id', $manage_u->user_id)
                        ->where('status', CommonModel::OFFER_AWAIT_OFFER)->where('confirm_time', '>=', now()->timestamp);
                }),
            ]
        ];
        $validator = Validator::make(array('offer_id' => $offer_id), $rules);

        if ($validator->passes())
        {   /*验证通过*/
            $this->ViewData['offer_info'] = $supplier->getSupplierOfferInfo($manage_u->user_id, $offer_id);
//            dump($this->ViewData);
            return view('supplier_offer_view', $this->ViewData);
        }
        else
        {
            return CommonModel::noPrivilegePrompt(request());/*没有权限*/
        }
    }

    /**
     * Ajax 供应商报价 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function OfferSubmit(Request $request)
    {
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
                        ->where('status', CommonModel::OFFER_AWAIT_OFFER)->where('confirm_time', '>=', now()->timestamp);
                }),
            ],
            'total_price' => 'required|numeric',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $supplier->supplierSubmitOffer($manage_u->user_id, $request->input('offer_id'), $request->input('total_price')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '供应商报价成功';
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
    public function SendGoods(Request $request)
    {
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
                        ->where('status', CommonModel::OFFER_PASSED);
                }),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $supplier->supplierSendGoods($manage_u->user_id, $request->input('offer_id')))
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

}