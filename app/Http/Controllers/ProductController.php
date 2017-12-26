<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */
namespace App\Http\Controllers;

use App\Entity\ProductSpec;
use App\Entity\SupplierPrice;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Tools\M3Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * 产品控制器
 * Class ProductController
 * @package App\Http\Controllers
 */
class ProductController extends Controller
{
    public $ViewData = array(); /*传递页面的数组*/

    /**
     * View 商品详情展示 页面
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ProductShow($id)
    {
        /*初始化*/
        $product = new Product();
        $manage_u = session('ManageUser');

        $this->ViewData['product_info'] = $product->getProductInfo($id);
        $this->ViewData['manage_user'] = $manage_u;

//        dump($this->ViewData);
        return view('product_show', $this->ViewData);
    }

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
//        dump($this->ViewData);
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
//        dump($this->ViewData);
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
     * Ajax 商品分类开启首页显示 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function CategoryIsIndex(Request $request)
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
                    $query->where('category_id', $GLOBALS['request']->input('category_id'))->where('is_delete', Product::CATEGORY_NO_DELETE)->where('is_index', Product::CATEGORY_NO_INDEX);
                }),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $product->isIndexProductCategory($request->input('category_id')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '商品分类开启首页显示';
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
     * Ajax 商品分类取消首页显示 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function CategoryNoIndex(Request $request)
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
                    $query->where('category_id', $GLOBALS['request']->input('category_id'))->where('is_delete', Product::CATEGORY_NO_DELETE)->where('is_index', Product::CATEGORY_IS_INDEX);
                }),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $product->isIndexProductCategory($request->input('category_id')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '商品分类取消首页显示';
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

    /**
     * View 商品列表 页面 (搜索条件参数: 商品分类)
     * @param int $category_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ProductList($category_id = 0)
    {
        /*初始化*/
        $product = new Product();
        $this->ViewData['product_list'] = array();
        $this->ViewData['category_list'] = $product->getProductCategoryList(array(), array(['product_category.sort', 'desc']), false);

        if ($category_id > 0)
        {
            $this->ViewData['product_list'] = $product->getProductList([['category_id', $category_id]]);
        }
        else
        {
            $this->ViewData['product_list'] = $product->getProductList();
        }

        $this->ViewData['page_search'] = array('category_id' => $category_id);
//        dump($this->ViewData);
        return view('product_list', $this->ViewData);
    }

    /**
     * View 商品添加 页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ProductAddPage()
    {
        /*初始化*/
        $product = new Product();
        $this->ViewData['category_list'] = $product->getProductCategoryList(array(), array(['product_category.sort', 'desc']), false);
        $this->ViewData['unit_list'] = $product->getProductCategoryUnitList();

        return view('product_add', $this->ViewData);
    }

    /**
     * View 商品编辑 页面
     * @param $product_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ProductEditPage($product_id)
    {
        /*初始化*/
        $product = new Product();
        $this->ViewData['category_list'] = $product->getProductCategoryList(array(), array(['product_category.sort', 'desc']), false);
        $this->ViewData['unit_list'] = $product->getProductCategoryUnitList();

        $this->ViewData['product_info'] = $product->getProductInfo($product_id);

//        dump($this->ViewData);
        return view('product_edit', $this->ViewData);
    }

    /**
     * Ajax 获取全部或单个分类商品列表
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ProductAjaxList(Request $request)
    {
        /*初始化*/
        $product = new Product();
        $m3result = new M3Result();
        /*验证规则*/
        $rules = [
            'category_id' => [
                'required',
                'integer',
                Rule::exists('product_category')->where(function ($query) use ($request)
                {
                    $query->where('category_id', $request->input('category_id'))->where('is_delete', Product::CATEGORY_NO_DELETE);
                }),
            ],
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() || $request->input('category_id') == 0)
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '获取商品列表成功';
            if ($request->input('category_id') > 0)
            {
                $m3result->data = $product->getProductList(array(['category_id', $request->input('category_id')]), array(['products.sort', 'desc']), false);
            }
            else
            {
                $m3result->data = $product->getProductList(array(), array(['products.sort', 'desc']), false);
            }
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
     * Ajax 商品新增 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ProductAddSubmit(Request $request)
    {
        /*初始化*/
        $product = new Product();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'sort' => 'required|integer',
            'product_thumb' => 'required|image|mimes:jpeg,gif,png|max:300',
            'product_unit' => 'required',
            'product_content' => 'string',
            'product_name' => 'required|unique:products,product_name',
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

        if ($validator->passes() && $product_info = $product->addProduct($request->all()))
        {   /*验证通过*/
            $m3result->code = 0;
            $m3result->messages = '商品添加成功';
            $m3result->data['product_info'] = $product_info;
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['product'] = $product->messages();
            if ($validator->errors()->has('product_thumb'))
            {
                $m3result->code = 2;
                $m3result->messages = '图片格式不正确或大小超过300KB';
            }
            if ($validator->errors()->has('product_name'))
            {
                $m3result->code = 3;
                $m3result->messages = '商品已存在,请更换名称';
            }
        }

        return $m3result->toJson();
    }

    /**
     * Ajax 商品修改 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ProductEditSubmit(Request $request)
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
                    $query->where('product_id', $GLOBALS['request']->input('product_id'));
                }),
            ],
            'product_name' => [
                'required',
                Rule::unique('products', 'product_name')->ignore($request->input('product_id'), 'product_id'),
            ],
            'product_unit' => 'required',
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
        $validator->sometimes('product_thumb', 'image|mimes:jpeg,gif,png|max:300', function ($input) use ($request)
        {
            return $request->hasFile('product_thumb');/*return true时才增加验证规则!*/
        });

        if ($validator->passes())
        {   /*验证通过*/
            if (!empty(ProductSpec::where('product_id', $request->input('product_id'))->first()))
            {
                $product->editProduct($request->all());
                $m3result->code = 0;
                $m3result->messages = '商品修改成功';
            }
            else
            {
                $m3result->code = 4;
                $m3result->messages = '商品规格不能为空';
            }

        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['product'] = $product->messages();
            if ($validator->errors()->has('product_thumb'))
            {
                $m3result->code = 2;
                $m3result->messages = '图片格式不正确或大小超过300KB';
            }
            if ($validator->errors()->has('product_name'))
            {
                $m3result->code = 3;
                $m3result->messages = '商品已存在,请更换名称';
            }
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
                    $query->where('product_id', $GLOBALS['request']->input('product_id'));
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

    /**
     * Ajax 新增商品规格 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ProductSpecAdd(Request $request)
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
                    $query->where('product_id', $GLOBALS['request']->input('product_id'));
                }),
            ],
            'spec_name' => 'required',
            'product_price' => 'required|numeric|min:0',
            'spec_image' => 'required|image|mimes:jpeg,gif,png|max:300',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $spec_info = $product->addSpec($request->all()))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '商品规格新增成功';
            $m3result->data['spec_info'] = $spec_info;
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['product'] = $product->messages();
            if ($validator->errors()->has('spec_image'))
            {
                $m3result->code = 2;
                $m3result->messages = '图片格式不正确或大小超过300KB';
            }
        }

        return $m3result->toJson();
    }

    /**
     * Ajax 编辑商品规格 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ProductSpecEdit(Request $request)
    {
        /*初始化*/
        $product = new Product();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'spec_id' => ['required', 'integer',
                Rule::exists('product_spec')->where(function ($query)
                {
                    $query->where('spec_id', $GLOBALS['request']->input('spec_id'));
                }),
            ],
            'spec_name' => 'required',
            'product_price' => 'required|numeric|min:0',
        ];
        $validator = Validator::make($request->all(), $rules);
        /*规格图片增加规则*/
        $validator->sometimes('spec_image', 'image|mimes:jpeg,gif,png|max:300', function ($input) use ($request)
        {
            return $request->hasFile('spec_image');/*return true时才增加验证规则!*/
        });

        if ($validator->passes() && $product->editSpec($request->all()))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '商品规格编辑成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['product'] = $product->messages();
            if ($validator->errors()->has('spec_image'))
            {
                $m3result->code = 2;
                $m3result->messages = '图片格式不正确或大小超过300KB';
            }
        }

        return $m3result->toJson();
    }

    /**
     * Ajax 删除商品规格 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ProductSpecDelete(Request $request)
    {
        /*初始化*/
        $product = new Product();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'spec_id' => ['required', 'integer',
                Rule::exists('product_spec')->where(function ($query)
                {
                    $query->where('spec_id', $GLOBALS['request']->input('spec_id'));
                }),
            ],
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $product->deleteSpec($request->input('spec_id')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '商品规格删除成功';
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
     * View 规格协议价列表 页面
     * @param $spec_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ProductSupplierPriceView($spec_id)
    {
        /*初始化*/
        $product = new Product();
        $user = new User();
        $this->ViewData['price_list'] = $product->getSpecSupplierPrice($spec_id);
        $this->ViewData['supplier_list'] = $user->getSupplierList();
        $this->ViewData['spec_id'] = $spec_id;

        return view('product_supplier_price', $this->ViewData);
    }

    /**
     * Ajax 新增供应商协议价 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ProductSupplierPriceAdd(Request $request)
    {
        /*初始化*/
        $product = new Product();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'user_id' => ['required', 'integer',
                Rule::exists('users')->where(function ($query)
                {
                    $query->where('user_id', $GLOBALS['request']->input('user_id'))->where('identity', User::SUPPLIER_ADMIN)->where('is_disable', User::NO_DISABLE);
                }),
            ],
            'spec_id' => ['required', 'integer',
                Rule::exists('product_spec')->where(function ($query)
                {
                    $query->where('spec_id', $GLOBALS['request']->input('spec_id'));
                }),
            ],
            'price' => 'required|numeric|min:0',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes())
        {   /*验证通过*/
            if (SupplierPrice::where('user_id', $request->input('user_id'))->where('spec_id', $request->input('spec_id'))->first() == false)
            {
                $price_info = $product->addSupplierPrice($request->all());
                $m3result->code = 0;
                $m3result->messages = '供应商协议价新增成功';
                $m3result->data['price_info'] = $price_info;
            }
            else
            {
                $m3result->code = 2;
                $m3result->messages = '供应商已有协议价,请查询修改';
            }

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
     * Ajax 编辑供应商协议价 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ProductSupplierPriceEdit(Request $request)
    {
        /*初始化*/
        $product = new Product();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'user_id' => ['required', 'integer',
                Rule::exists('users')->where(function ($query)
                {
                    $query->where('user_id', $GLOBALS['request']->input('user_id'))->where('identity', User::SUPPLIER_ADMIN)->where('is_disable', User::NO_DISABLE);
                }),
            ],
            'price_id' => ['required', 'integer',
                Rule::exists('supplier_price')->where(function ($query)
                {
                    $query->where('price_id', $GLOBALS['request']->input('price_id'));
                }),
            ],
            'price' => 'required|numeric|min:0',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes())
        {   /*验证通过*/
            if (SupplierPrice::where('user_id', $request->input('user_id'))->where('spec_id', $request->input('spec_id'))->first() == false)
            {
                $price_info = $product->editSupplierPrice($request->all());
                $m3result->code = 0;
                $m3result->messages = '供应商协议价编辑成功';
                $m3result->data['price_info'] = $price_info;
            }
            else
            {
                $m3result->code = 2;
                $m3result->messages = '供应商已有协议价,请查询修改';
            }

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
     * Ajax 删除供应商协议价 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ProductSupplierPriceDelete(Request $request)
    {
        /*初始化*/
        $product = new Product();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'price_id' => ['required', 'integer',
                Rule::exists('supplier_price')->where(function ($query)
                {
                    $query->where('price_id', $GLOBALS['request']->input('price_id'));
                }),
            ],
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $product->deleteSupplierPrice($request->input('price_id')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '供应商协议价删除成功';
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