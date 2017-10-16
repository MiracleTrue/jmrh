<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */
namespace App\Http\Controllers;
use App\Models\Product;
use App\Tools\M3Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * 产品控制器
 * Class ProductController
 * @package App\Http\Controllers
 */
class ProductController extends Controller
{
    public $ViewData = array(); /*传递页面的数组*/

    public function CategoryList()
    {
        /*初始化*/
        $product = new Product();
        $this->ViewData['category_list'] = array();

        $this->ViewData['category_list']= $product->getProductCategoryList();
        dump($this->ViewData);
        return view('welcome', $this->ViewData);
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
        return view('welcome', $this->ViewData);
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
            'sort' =>'required|integer',
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
            'sort' =>'required|integer',
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
     * Ajax 商品分类删除 请求处理
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

    }

    public function ProductView($id = 0)
    {

    }

    public function ProductAdd(Request $request)
    {

    }

    public function ProductEdit(Request $request)
    {

    }

    public function ProductDelete(Request $request)
    {

    }
}