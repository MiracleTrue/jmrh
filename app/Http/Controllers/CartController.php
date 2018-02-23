<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Tools\M3Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * 购物车控制器
 * Class CartController
 * @package App\Http\Controllers
 */
class CartController extends Controller
{
    public $ViewData = array(); /*传递页面的数组*/

    /**
     * View 购物车列表 页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function CartList()
    {
        /*初始化*/
        $cart = new Cart();
        $manage_u = session('ManageUser');
        $where = array();
        $this->ViewData['cart_list'] = array();
        $this->ViewData['manage_user'] = $manage_u;

        /*加入sql条件购物车所有者id*/
        array_push($where, ['shopping_cart.user_id', '=', $manage_u->user_id]);

        $this->ViewData['cart_list'] = $cart->getCartList($where);

        return view('cart_list', $this->ViewData);
    }

    /**
     * Ajax 加入购物车 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function CartAddProduct(Request $request)
    {
//        $arr = array(
//            'product_id' => '143',
//            'spec_id' => '139',
//            'army_receive_time' => '2018-2-6',
//            'contact_tel' => '15648974897',
//            'contact_person' => '张三',
//            'note' => null,
//            'product_number' => 10
//
//        );
//        $request->merge($arr);

        /*初始化*/
        $cart = new Cart();
        $m3result = new M3Result();
        $manage_u = session('ManageUser');

        /*验证规则*/
        $rules = [
            'army_receive_time' => 'nullable|date',
            'contact_person' => 'nullable|string',
            'contact_tel' => 'nullable|string',
            'note' => 'nullable|string',
            'product_id' => 'required|integer|exists:products,product_id',
            'product_number' => 'required|numeric',
            'spec_id' => ['required', 'integer',
                Rule::exists('product_spec', 'spec_id')->where(function ($query) use ($request)
                {
                    $query->where('product_id', $request->input('product_id'));
                })
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $cart->addProductToCart($manage_u->user_id, $request->input('product_id'), $request->input('spec_id'), $request->input('product_number'), $request->all()))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '加入购物车成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['cart'] = $cart->messages();
        }
        return $m3result->toJson();
    }

    /**
     * Ajax 购物车删除产品(单个或多个) 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function CartDeleteProducts(Request $request)
    {
//        $arr = array(
//            'cart_id' => 5,
//        );
//        $request->merge($arr);
        /*初始化*/
        $cart = new Cart();
        $m3result = new M3Result();
        $manage_u = session('ManageUser');

        /*验证规则*/
        $rules = [
            'cart_id.*' => 'required|integer|exists:shopping_cart,cart_id',
        ];

        $validator = Validator::make(array('cart_id' => explode(',', $request->input('cart_id'))), $rules);

        if ($validator->passes() && $cart->deleteProductsFromCart($manage_u->user_id, explode(',', $request->input('cart_id'))))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '删除成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['cart'] = $cart->messages();
        }
        return $m3result->toJson();
    }

    /**
     * Ajax 购物车产品改变数量 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function CartChangeProductNumber(Request $request)
    {
        /*初始化*/
        $cart = new Cart();
        $m3result = new M3Result();
        $manage_u = session('ManageUser');

        /*验证规则*/
        $rules = [
            'cart_id' => 'required|integer|exists:shopping_cart,cart_id',
            'product_number' => 'required|numeric|min:0.01'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $cart->changeProductNumberFromCart($manage_u->user_id, $request->input('cart_id'), $request->input('product_number')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '数量修改成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['cart'] = $cart->messages();
        }
        return $m3result->toJson();
    }
}