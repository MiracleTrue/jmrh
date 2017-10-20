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
     * @param null $status
     * @param null $create_time
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function NeedList($status = null, $create_time = null)
    {
        /*初始化*/
        $army = new Army();
        $this->ViewData['order_list'] = array();
        $where = array();
        $or_where = array();

        /*条件搜索*/
        switch ($status)
        {
            case '待确认' :
                array_push($where, ['orders.status', '=', $army::ORDER_AWAIT_ALLOCATION]);
                break;
            case '已确认':
                array_push($where, ['orders.status', '=', $army::ORDER_AGAIN_ALLOCATION]);
                array_push($or_where, ['orders.status', '=', $army::ORDER_ALLOCATION_SUPPLIER]);
                array_push($or_where, ['orders.status', '=', $army::ORDER_SUPPLIER_SELECTED]);
                array_push($or_where, ['orders.status', '=', $army::ORDER_SUPPLIER_SEND]);
                array_push($or_where, ['orders.status', '=', $army::ORDER_SUPPLIER_RECEIVE]);
                array_push($or_where, ['orders.status', '=', $army::ORDER_ALLOCATION_PLATFORM]);
                break;
            case '已发货' :
                array_push($where, ['orders.status', '=', $army::ORDER_SEND_ARMY]);
                break;
            case '已到货' :
                array_push($where, ['orders.status', '=', $army::ORDER_RECEIVE_ARMY]);
                break;
        }
        if (!empty($create_time) && strtotime($create_time))
        {
            $dt = Carbon::parse($create_time);
            $start_dt = Carbon::create($dt->year, $dt->month, $dt->day, 0, 0, 0)->timestamp;
            $end_dt = Carbon::create($dt->year, $dt->month, $dt->day, 0, 0, 0)->addDay()->subSecond()->timestamp;
            array_push($where, ['orders.create_time', '>=',$start_dt]);
            array_push($where, ['orders.create_time', '<=',$end_dt]);
        }
        $this->ViewData['order_list'] = $army->getOrderList($where, $or_where);

        dump($this->ViewData);
        return view('army_need_list', $this->ViewData);
    }

    /**
     * View 军方需求添加与编辑 页面
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function NeedView($id)
    {
        /*初始化*/
        $army = new Army();
        $this->ViewData['order_info'] = array();
        if ($id > 0)
        {
            $this->ViewData['order_info'] = $army->getOrderInfo($id);
        }

        dump($this->ViewData);
        return view('army_need_view', $this->ViewData);
    }

    /**
     * Ajax 发布军方需求 请求处理
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
            'product_name' => 'required',
            'product_number' => 'required|integer',
            'product_unit' => 'required',
            'army_receive_time' => 'required|date|after:now'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $army->releaseNeed($request->all()))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '军方需求发布成功';
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
     * Ajax 修改军方需求 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function NeedEdit(Request $request)
    {
        /*初始化*/
        $army = new Army();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders')->where(function ($query)
                {
                    $query->where('order_id', $GLOBALS['request']->input('order_id'))
                        ->where('is_delete', CommonModel::ORDER_NO_DELETE)
                        ->where('type', Army::ORDER_TYPE_ARMY)
                        ->where('status', CommonModel::ORDER_AWAIT_ALLOCATION);
                }),
            ],
            'product_name' => 'required',
            'product_number' => 'required|integer',
            'product_unit' => 'required',
            'army_receive_time' => 'required|date|after:now'
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
        $army = new Army();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders')->where(function ($query)
                {
                    $query->where('order_id', $GLOBALS['request']->input('order_id'))
                        ->where('is_delete', CommonModel::ORDER_NO_DELETE)
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


    /*********************************************************************************/
    /**
     * View 商品分类列表 页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function CategoryList()
    {
        /*初始化*/
        $product = new Product();
        $this->ViewData['category_list'] = array();

        $this->ViewData['category_list'] = $product->getProductCategoryList();
        dump($this->ViewData);
        return view('category_list', $this->ViewData);
    }

    /**
     * View 商品分类添加与编辑 页面
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function CategoryView($id = 0)
    {
        /*初始化*/
        $product = new Product();
        $this->ViewData['category_info'] = array();

        if ($id > 0)
        {
            $this->ViewData['category_info'] = $product->getProductCategory($id);
        }
        dump($this->ViewData);
        return view('category_view', $this->ViewData);
    }

    /**
     * Ajax 商品分类添加 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function CategoryAdd(Request $request)
    {
        /*初始化*/
        $product = new Product();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'category_name' => 'required',
            'unit' => 'required',
            'sort' => 'required|integer',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $product->addProductCategory($request->all()))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '商品分类添加成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['product'] = $product->messages();
        }

        return $m3result->toJson();
    }

    /**
     * Ajax 商品分类修改 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function CategoryEdit(Request $request)
    {
        /*初始化*/
        $product = new Product();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'category_id' => [
                'required',
                'integer',
                Rule::exists('product_category')->where(function ($query)
                {
                    $query->where('category_id', $GLOBALS['request']->input('category_id'));
                }),
            ],
            'category_name' => 'required',
            'unit' => 'required',
            'sort' => 'required|integer',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $product->editProductCategory($request->all()))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '商品分类修改成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['product'] = $product->messages();
        }

        return $m3result->toJson();
    }

    /**
     * Ajax 商品分类删除(伪删除) 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function CategoryDelete(Request $request)
    {
        /*初始化*/
        $product = new Product();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'category_id' => [
                'required',
                'integer',
                Rule::exists('product_category')->where(function ($query)
                {
                    $query->where('category_id', $GLOBALS['request']->input('category_id'))->where('is_delete', Product::CATEGORY_NO_DELETE);
                }),
            ]
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes() && $product->deleteProductCategory($request->input('category_id')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '商品分类删除成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['product'] = $product->messages();
        }

        return $m3result->toJson();
    }

    public function ProductList()
    {
        /*初始化*/
        $product = new Product();
        $this->ViewData['product_list'] = array();

        $this->ViewData['product_list'] = $product->getProductList();
        dump($this->ViewData);
        return view('product_list', $this->ViewData);
    }

    /**
     * View 商品添加与编辑 页面
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ProductView($id = 0)
    {
        /*初始化*/
        $product = new Product();
        $this->ViewData['product_info'] = array();
        if ($id > 0)
        {
            $this->ViewData['product_info'] = $product->getProductInfo($id);
        }

        dump($this->ViewData);
        return view('product_view', $this->ViewData);
    }

    /**
     * Ajax 商品新增 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ProductAdd(Request $request)
    {
        /*初始化*/
        $product = new Product();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'product_name' => 'required',
            'sort' => 'required|integer',
            'product_image' => 'required|file|image',
            'product_content' => 'string',
            'category_id' => [
                'required',
                'integer',
                Rule::exists('product_category')->where(function ($query)
                {
                    $query->where('category_id', $GLOBALS['request']->input('category_id'))->where('is_delete', Product::CATEGORY_NO_DELETE);
                }),
            ],
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $product->addProduct($request->all()))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '商品添加成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['product'] = $product->messages();
        }

        return $m3result->toJson();
    }

    /**
     * Ajax 商品修改 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ProductEdit(Request $request)
    {
        /*初始化*/
        $product = new Product();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products')->where(function ($query)
                {
                    $query->where('product_id', $GLOBALS['request']->input('product_id'))->where('is_delete', Product::PRODUCT_NO_DELETE);
                }),
            ],
            'product_name' => 'required',
            'sort' => 'required|integer',
            'product_content' => 'string',
            'category_id' => [
                'required',
                'integer',
                Rule::exists('product_category')->where(function ($query)
                {
                    $query->where('category_id', $GLOBALS['request']->input('category_id'))->where('is_delete', Product::CATEGORY_NO_DELETE);
                }),
            ],
        ];
        $validator = Validator::make($request->all(), $rules);
        /*缩略图增加规则*/
        $validator->sometimes('product_image', 'file|image', function ($input) use ($request)
        {
            return $request->hasFile('product_image');/*return true时才增加验证规则!*/
        });

        if ($validator->passes() && $product->editProduct($request->all()))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '商品修改成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['product'] = $product->messages();
        }

        return $m3result->toJson();
    }

    /**
     * Ajax 商品删除(伪删除) 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ProductDelete(Request $request)
    {
        /*初始化*/
        $product = new Product();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products')->where(function ($query)
                {
                    $query->where('product_id', $GLOBALS['request']->input('product_id'))->where('is_delete', Product::PRODUCT_NO_DELETE);
                }),
            ],
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $product->deleteProduct($request->input('product_id')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '商品删除成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['product'] = $product->messages();
        }

        return $m3result->toJson();
    }
}