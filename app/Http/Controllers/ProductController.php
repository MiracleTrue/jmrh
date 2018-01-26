<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */
namespace App\Http\Controllers;

use App\Entity\ProductSpec;
use App\Models\MyFile;
use App\Models\Product;
use App\Models\User;
use App\Tools\M3Result;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
     * @param $product_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ProductShow($product_id)
    {
        /*初始化*/
        $product = new Product();
        $manage_u = session('ManageUser');

        $this->ViewData['product_info'] = $product->getProductInfo($product_id);
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
        $user = new User();
        $this->ViewData['category_info'] = array();
        $this->ViewData['platform_user_list'] = $user->getPlatformUserList();
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
            'sort' => 'required|integer|between:-9999,9999',
        ];
        $validator = Validator::make($request->all(), $rules);
        /*分类负责人 验证增加规则*/
        $validator->sometimes('manage_user_id', ['integer',
            Rule::exists('users', 'user_id')->where('user_id', $request->input('manage_user_id'))->where('is_disable', User::NO_DISABLE)->where('identity', User::PLATFORM_ADMIN)
        ], function ($input)
        {
            return !empty($input->manage_user_id);/*return true时才增加验证规则!*/
        });

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
            'sort' => 'required|integer|between:-9999,9999',
        ];
        $validator = Validator::make($request->all(), $rules);
        /*分类负责人 验证增加规则*/
        $validator->sometimes('manage_user_id', ['integer',
            Rule::exists('users', 'user_id')->where('user_id', $request->input('manage_user_id'))->where('is_disable', User::NO_DISABLE)->where('identity', User::PLATFORM_ADMIN)
        ], function ($input)
        {
            return !empty($input->manage_user_id);/*return true时才增加验证规则!*/
        });

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
                    $query->where('category_id', $GLOBALS['request']->input('category_id'))->where('is_index', Product::CATEGORY_NO_INDEX);
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
                    $query->where('category_id', $GLOBALS['request']->input('category_id'))->where('is_index', Product::CATEGORY_IS_INDEX);
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
                    $query->where('category_id', $GLOBALS['request']->input('category_id'));
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
     * View 商品添加与编辑 页面
     * @param $product_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ProductView($product_id = 0)
    {
        /*初始化*/
        $product = new Product();
        $user = new User();
        $this->ViewData['category_list'] = $product->getProductCategoryList(array(), array(['product_category.sort', 'desc']), false);
        $this->ViewData['unit_list'] = $product->getProductCategoryUnitList();
        $this->ViewData['supplier_list'] = $user->getSupplierList();

        if ($product_id > 0)
        {
            $this->ViewData['product_info'] = $product->getProductInfo($product_id);
        }

//        dump($this->ViewData);
        return view('product_view', $this->ViewData);
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
                    $query->where('category_id', $request->input('category_id'));
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

//        dd($m3result->data);
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
            'sort' => 'required|integer|between:-9999,9999',
            'product_thumb' => 'required|image|mimes:jpeg,gif,png|max:300',
            'product_content' => 'string',
            'product_name' => 'required|unique:products,product_name',
            'category_id' => [
                'required',
                'integer',
                Rule::exists('product_category')->where(function ($query)
                {
                    $query->where('category_id', $GLOBALS['request']->input('category_id'));
                }),
            ],
            'spec_json' => 'required|json'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && is_array($spec_json_arr = json_decode($request->input('spec_json'), true)))
        {
            /*验证规格*/
            $json_rules = [
                '*.spec_name' => 'required',
                '*.spec_unit' => 'required',
                '*.product_price' => 'required|numeric|min:0',
                '*.image_thumb' => 'required',
                '*.image_original' => 'required',
                '*.supplier_price.*.price' => 'required|numeric|min:0',
                '*.supplier_price.*.user_id' => ['required', 'integer',
                    Rule::exists('users')->where(function ($query)
                    {
                        $query->where('identity', User::SUPPLIER_ADMIN)->where('is_disable', User::NO_DISABLE);
                    }),
                ]
            ];

            $validator_json = Validator::make($spec_json_arr, $json_rules);
            if ($validator_json->passes() && $product->addProduct($request->all()))
            {
                $m3result->code = 0;
                $m3result->messages = '商品添加成功';
            }
            else
            {
                $m3result->code = 4;
                $m3result->messages = '商品规格格式不正确';
                $m3result->data['validator'] = $validator_json->messages();
                $m3result->data['product'] = $product->messages();
            }
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            if ($validator->errors()->has('product_thumb') && $request->filled('product_thumb'))
            {
                $m3result->code = 2;
                $m3result->messages = '图片格式不正确或大小超过300KB';
            }
            if ($validator->errors()->has('product_name') && $request->filled('product_name'))
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
            'sort' => 'required|integer|between:-9999,9999',
            'product_content' => 'string',
            'category_id' => [
                'required',
                'integer',
                Rule::exists('product_category')->where(function ($query)
                {
                    $query->where('category_id', $GLOBALS['request']->input('category_id'));
                }),
            ],
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
            'spec_json' => 'required|json'
        ];
        $validator = Validator::make($request->all(), $rules);
        /*缩略图增加规则*/
        $validator->sometimes('product_thumb', 'image|mimes:jpeg,gif,png|max:300', function ($input) use ($request)
        {
            return $request->hasFile('product_thumb');/*return true时才增加验证规则!*/
        });

        if ($validator->passes() && is_array($spec_json_arr = json_decode($request->input('spec_json'), true)))
        {
            /*验证规格*/
            $json_rules = [
                '*.spec_name' => 'required',
                '*.spec_unit' => 'required',
                '*.product_price' => 'required|numeric|min:0',
                '*.image_thumb' => 'required',
                '*.image_original' => 'required',
                '*.supplier_price.*.price' => 'required|numeric|min:0',
                '*.supplier_price.*.user_id' => ['required', 'integer',
                    Rule::exists('users')->where(function ($query)
                    {
                        $query->where('identity', User::SUPPLIER_ADMIN)->where('is_disable', User::NO_DISABLE);
                    }),
                ]
            ];

            $validator_json = Validator::make($spec_json_arr, $json_rules);
            if ($validator_json->passes() && $product->editProduct($request->all()))
            {
                $m3result->code = 0;
                $m3result->messages = '商品修改成功';
            }
            else
            {
                $m3result->code = 4;
                $m3result->messages = '商品规格格式不正确';
                $m3result->data['validator'] = $validator_json->messages();
                $m3result->data['product'] = $product->messages();
            }
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            if ($validator->errors()->has('product_thumb') && $request->filled('product_thumb'))
            {
                $m3result->code = 2;
                $m3result->messages = '图片格式不正确或大小超过300KB';
            }
            if ($validator->errors()->has('product_name') && $request->filled('product_name'))
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
     * Ajax 上传规格图片 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ProductUploadSpecImage(Request $request)
    {
        /*初始化*/
        $my_file = new MyFile();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'spec_image' => 'required|image|mimes:jpeg,gif,png|max:300',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes())
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '商品规格图片上传成功';
            $m3result->data['spec_image']['image_thumb'] = $my_file->uploadThumb($request->file('spec_image'));
            $m3result->data['spec_image']['image_original'] = $my_file->uploadOriginal($request->file('spec_image'));
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '图片格式不正确或大小超过300KB';
            $m3result->data['validator'] = $validator->messages();
        }
        return $m3result->toJson();
    }

}