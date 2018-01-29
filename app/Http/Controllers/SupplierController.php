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
//            'offer_id' => '1',
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
//            'offer_id' => '1',
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
//            'offer_id' => '1',
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

}