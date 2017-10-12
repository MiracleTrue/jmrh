<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/15 0015
 * Time  : 16:02
 */
namespace App\Http\Controllers\Admin;
use App\Entity\GoodsBrand;
use App\Entity\GoodsCategory;
use App\Models\Attributes;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Goods;
use App\Models\Menu;
use App\Tools\M3Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class 后台 商品管理控制器
 */
class GoodsController extends CommonController
{
    public $ViewData = array(); /*传递页面的数组*/

    /**
     * 商品分类 首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function CategoryIndex()
    {
        /*初始化*/
        $category  = new Category();

        $this->ViewData['nav_position'] = Menu::getAdminPosition();/*面包屑*/
        $this->ViewData['category_tree'] = $category->getGoodsCategoryTree();

        return view('admin.goods_category_index',$this->ViewData);
    }

    /**
     * 商品分类 新增与编辑页面
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function CategoryView($id = 0)
    {
        /*初始化*/
        $category  = new Category();
        $this->ViewData['category_info'] = null;
        $this->ViewData['category_tree'] = $category->getGoodsCategoryTree();

        if($id > 0)
        {
            $this->ViewData['category_info'] = GoodsCategory::findOrFail($id);
            if($this->ViewData['category_info']->parent_id > 0)
            {
                $this->ViewData['category_info']->parent_info = GoodsCategory::findOrFail($this->ViewData['category_info']->parent_id);

            }
            else
            {
                $temp_arr = array(
                    'category_name' => __('admin.topCategory'),
                    'category_en_name' => __('admin.topCategory')
                );
                $this->ViewData['category_info']->parent_info = $temp_arr;
            }
        }

        return view('admin.goods_category_edit',$this->ViewData);
    }

    /**
     * 商品分类 Ajax新增与编辑 提交处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function CategoryEditSubmit(Request $request)
    {
        /*初始化*/
        $admin_u  = session('AdminUser');
        $category  = new Category();
        $m3result = new M3Result();

        if($request->input('category_id') == 0)/*新增商品分类*/
        {
            /*验证规则*/
            $rules = [
                'category_id'  => 'required',
                'category_name'   => 'required',
                'alias_name'  => 'required',
                'category_sort'   => 'required|integer',
                'parent_id'   => 'required|integer',
            ];
            $validator = Validator::make($request->all(), $rules);

            /*按条件增加规则*/
            $validator->sometimes('parent_id', 'exists:goods_category,category_id', function ($input) {
                return $input->parent_id !=0;/*return true时才增加验证规则!*/
            });

            if($validator->passes() && $category->addGoodsCategory($request))
            {   /*验证通过并且添加成功*/
                $m3result->code    = 0;
                $m3result->messages= __('admin.success');
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
                $m3result->data['validator']    = $validator->messages();
                $m3result->data['category']      = $category->messages();
            }
        }
        else if($request->input('category_id') > 0)/*编辑商品分类*/
        {
            /*验证规则*/
            $rules = [
                'category_id'  => 'required',
                'category_name'   => 'required',
                'alias_name'  => 'required',
                'category_sort'   => 'required|integer',
                'parent_id'   => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $rules);

            /*按条件增加规则*/
            $validator->sometimes('parent_id', 'exists:goods_category,category_id', function ($input) {
                return $input->parent_id !=0;/*return true时才增加验证规则!*/
            });

            if($validator->passes() && $category->editGoodsCategory($request))
            {   /*验证通过并且更新成功*/
                $m3result->code    = 0;
                $m3result->messages= __('admin.success');
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
                $m3result->data['validator']    = $validator->messages();
                $m3result->data['category']      = $category->messages();
            }
        }
        else
        {
            $m3result->code    = 2;
            $m3result->messages= '无效数据';
        }

        return $m3result->toJson();
    }

    /**
     * 商品分类 Ajax删除提交
     * @param Request $request
     * @return \App\Tools\json
     */
    public function CategoryDeleteOne(Request $request)
    {
        /*初始化*/
        $category  = new Category();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'category_id'     => [
                'required',
                'integer',
                Rule::exists('goods_category')->where(function ($query) {
                    $query->where('category_id',$GLOBALS['request']->input('category_id'));
                }),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        if($validator->passes() && $category->deleteGoodsCategory($request->input('category_id')))
        {   /*验证通过*/
            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->data['validator']    = $validator->messages();
            $m3result->data['category']      = $category->messages();
            if($m3result->data['category']['code'] == 1)
            {
                $m3result->code    = 2;
                $m3result->messages= $m3result->data['category']['messages'];
            }
            elseif($m3result->data['category']['code'] == 2)
            {
                $m3result->code    = 3;
                $m3result->messages= $m3result->data['category']['messages'];
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
            }
        }
        return $m3result->toJson();
    }


    /**
     * 获取商品添加时分类对应的相关数据的请求
     * @param Request $request
     * @return \App\Tools\json
     */
    public function CategoryGetRelevance(Request $request)
    {
        /*初始化*/
        $admin_u  = session('AdminUser');
        $category  = new Category();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'type' => 'required',
            'category_id' => 'required|integer'
        ];
        $validator = Validator::make($request->all(), $rules);

        $request->merge(array('merchant_id' => $admin_u->merchant_id));/*将session中的merchant_id加入$request*/
        if($validator->passes() && $m3result->data = $category->getGoodsAddRelationCategory($request->all()))
        {   /*验证通过*/
            $m3result->code    = 0;
            $m3result->messages= __('admin.successData');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failedData');
            $m3result->data    = $validator->messages();
        }
        return $m3result->toJson();
    }

    /**
     * 商品品牌列表页面(传如$category_id显示该分类下的所有商品品牌)
     * @param int $category_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function BrandIndex($category_id = 0)
    {
        /*初始化*/
        $category  = new Category();
        $brand     = new Brand();
        $this->ViewData['brand_list'] = array();
        $this->ViewData['nav_position']  = Menu::getAdminPosition();/*面包屑*/
        $this->ViewData['category_tree'] = $category->getGoodsCategoryTree('brand');

        if($category_id > 0)
        {
            $this->ViewData['category_info'] = $category->getOneCategoryRelationBrand($category_id);
            $this->ViewData['brand_list'] =  $brand->getGoodsBrandList([['category_id',$category_id]]);
        }
        else
        {
            $this->ViewData['brand_list'] = $brand->getGoodsBrandList();
        }

        return view('admin.goods_brand_index',$this->ViewData);
    }

    /**
     * 商品品牌 编辑与新增页面
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function BrandView($id = 0)
    {
        /*初始化*/
        $category  = new Category();
        $brand     = new Brand();
        $this->ViewData['brand_info'] = null;
        $this->ViewData['category_tree'] = $category->getGoodsCategoryTree('brand');

        if($id > 0)
        {
            $this->ViewData['brand_info'] = $brand->getOneGoodsBrandRelationCategory($id);
        }

        return view('admin.goods_brand_edit',$this->ViewData);
    }

    /**
     * 商品品牌 Ajax新增与编辑 提交处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function BrandEditSubmit(Request $request)
    {
        /*初始化*/
        $admin_u  = session('AdminUser');
        $brand     = new Brand();
        $m3result = new M3Result();

        if($request->input('brand_id') == 0)/*新增商品品牌*/
        {
            /*验证规则*/
            $rules = [
                'brand_id'  => 'required|integer',
                'brand_name'   => 'required',
                'brand_description'  => 'required',
                'brand_sort'   => 'required|integer',
                'brand_logo' => 'file|image',
                'category_id'     => [
                    'required',
                    'integer',
                    Rule::exists('goods_category')->where(function ($query) {
                        $query->where('category_id',$GLOBALS['request']->input('category_id'));
                    }),
                ]
            ];
            $validator = Validator::make($request->all(), $rules);

            $request->merge(array('merchant_id' => $admin_u->merchant_id));/*将session中的merchant_id加入$request*/
            if($validator->passes() && $brand->addGoodsBrand($request->all()))
            {   /*验证通过并且添加成功*/
                $m3result->code    = 0;
                $m3result->messages= __('admin.success');
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
                $m3result->data['validator']    = $validator->messages();
                $m3result->data['brand']      = $brand->messages();
            }
        }
        else if($request->input('brand_id') > 0)/*编辑商品品牌*/
        {
            /*验证规则*/
            $rules = [
                'brand_id'  => 'required|integer',
                'brand_name'   => 'required',
                'brand_description'  => 'required',
                'brand_sort'   => 'required|integer',
                'category_id'     => [
                    'required',
                    'integer',
                    Rule::exists('goods_category')->where(function ($query) {
                        $query->where('category_id',$GLOBALS['request']->input('category_id'));
                    }),
                ]
            ];
            $validator = Validator::make($request->all(), $rules);

            if($validator->passes() && $brand->editGoodsBrand($request->all()))
            {   /*验证通过并且更新成功*/
                $m3result->code    = 0;
                $m3result->messages= __('admin.success');
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
                $m3result->data['validator']    = $validator->messages();
                $m3result->data['brand']      = $brand->messages();
            }
        }
        else
        {
            $m3result->code    = 2;
            $m3result->messages= '无效数据';
        }

        return $m3result->toJson();
    }

    /**
     * 商品品牌 Ajax删除提交
     * @param Request $request
     * @return \App\Tools\json
     */
    public function BrandDeleteOne(Request $request)
    {
        /*初始化*/
        $brand    = new Brand();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'brand_id'    => [
                'required',
                'integer',
                Rule::exists('goods_brand')->where(function ($query) {
                    $query->where('brand_id',$GLOBALS['request']->input('brand_id'));
                }),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->passes() && $brand->deleteGoodsBrand($request->input('brand_id')))
        {   /*验证通过*/
            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failed');
            $m3result->data['validator']    = $validator->messages();
            $m3result->data['brand']        = $brand->messages();
        }
        return $m3result->toJson();
    }

    /**
     * 商品属性列表页面(传如$category_id显示该分类下的所有商品属性)
     * @param int $category_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function AttributesIndex($category_id = 0)
    {
        /*初始化*/
        $category  = new Category();
        $attributes= new Attributes();

        $this->ViewData['attr_list'] = array();
        $this->ViewData['nav_position']  = Menu::getAdminPosition();/*面包屑*/
        $this->ViewData['category_tree'] = $category->getGoodsCategoryTree('attr');

        if($category_id > 0)
        {
            $this->ViewData['category_info'] = $category->getOneCategoryRelationAttributes($category_id);
            $this->ViewData['attr_list'] = $attributes->getGoodsAttributesAll([['category_id',$category_id]]);
        }
        else
        {
            $this->ViewData['attr_list'] = $attributes->getGoodsAttributesAll();
        }
        return view('admin.goods_attr_index',$this->ViewData);
    }

    /**
     * 商品属性 编辑与新增页面
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function AttributesView($id = 0)
    {
        /*初始化*/
        $attributes = new Attributes();
        $category  = new Category();
        $this->ViewData['attr_info'] = null;
        $this->ViewData['category_tree'] = $category->getGoodsCategoryTree('attr');

        if($id > 0)
        {
            $this->ViewData['attr_info'] =$attributes->getOneGoodsAttributesRelationCategory($id);
        }
        return view('admin.goods_attr_edit',$this->ViewData);
    }

    /**
     * 商品属性 Ajax新增与编辑 提交处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function AttributesEditSubmit(Request $request)
    {

        /*初始化*/
        $attributes = new Attributes();
        $m3result = new M3Result();

        if($request->input('attr_id') == 0)/*新增商品属性*/
        {
            /*验证规则*/
            $rules = [
                'attr_id'  => 'required|integer',
                'attr_name'   => 'required',
                'category_id'     => [
                    'required',
                    'integer',
                    Rule::exists('goods_category')->where(function ($query) {
                        $query->where('category_id',$GLOBALS['request']->input('category_id'));
                    }),
                ]
            ];
            $validator = Validator::make($request->all(), $rules);
            /*按条件增加规则*/
            $validator->sometimes('select_attr', 'json', function ($input) {
                return !empty($input->select_attr);/*return true时才增加验证规则!*/
            });
            /*按条件增加规则*/
            $validator->sometimes('show_attr', 'json', function ($input) {
                return !empty($input->show_attr);/*return true时才增加验证规则!*/
            });

            if($validator->passes() && $attributes->addGoodsAttributes($request->all()))
            {   /*验证通过并且添加成功*/
                $m3result->code    = 0;
                $m3result->messages= __('admin.success');
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
                $m3result->data['validator']    = $validator->messages();
                $m3result->data['attributes']   = $attributes->messages();
            }
        }
        else if($request->input('attr_id') > 0)/*编辑商品属性*/
        {
            /*验证规则*/
            $rules = [
                'attr_id'  => 'required|integer',
                'attr_name'   => 'required',
                'category_id'     => [
                    'required',
                    'integer',
                    Rule::exists('goods_category')->where(function ($query) {
                        $query->where('category_id',$GLOBALS['request']->input('category_id'));
                    }),
                ]
            ];
            $validator = Validator::make($request->all(), $rules);
            /*按条件增加规则*/
            $validator->sometimes('select_attr', 'json', function ($input) {
                return !empty($input->select_attr);/*return true时才增加验证规则!*/
            });
            /*按条件增加规则*/
            $validator->sometimes('show_attr', 'json', function ($input) {
                return !empty($input->show_attr);/*return true时才增加验证规则!*/
            });

            if($validator->passes() && $attributes->editGoodsAttributes($request->all()))
            {   /*验证通过并且更新成功*/
                $m3result->code    = 0;
                $m3result->messages= __('admin.success');
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
                $m3result->data['validator']    = $validator->messages();
                $m3result->data['attributes']        = $attributes->messages();
            }
        }
        else
        {
            $m3result->code    = 2;
            $m3result->messages= '无效数据';
        }
        return $m3result->toJson();
    }

    /**
     * 商品属性 Ajax删除提交
     * @param Request $request
     * @return \App\Tools\json
     */
    public function AttributesDeleteOne(Request $request)
    {
        /*初始化*/
        $attributes = new Attributes();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'attr_id'    => [
                'required',
                'integer',
                Rule::exists('goods_attributes')->where(function ($query) {
                    $query->where('attr_id',$GLOBALS['request']->input('attr_id'));
                }),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->passes() && $attributes->deleteGoodsAttributes($request->input('attr_id')))
        {   /*验证通过*/
            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failed');
            $m3result->data['validator']    = $validator->messages();
            $m3result->data['attributes']   = $attributes->messages();
        }
        return $m3result->toJson();
    }

    /**
     * 商品列表页面(传如$category_id显示该分类下的所有商品)
     * @param int $category_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function GoodsIndex($category_id = 0)
    {
        /*初始化*/
        $category  = new Category();
        $goods     = new Goods();

        $this->ViewData['goods_list'] = array();
        $this->ViewData['nav_position']  = Menu::getAdminPosition();/*面包屑*/
        $this->ViewData['category_tree'] = $category->getGoodsCategoryTree();

        if($category_id > 0)
        {
            $this->ViewData['category_info'] = $category->getOneCategoryRelationAttributes($category_id);
            $this->ViewData['goods_list'] = $goods->getGoodsListAll([['is_delete',$goods::NO_DELETE],['category_id',$category_id]]);
        }
        else
        {
            $this->ViewData['goods_list'] = $goods->getGoodsListAll([['is_delete',$goods::NO_DELETE]]);
        }
        return view('admin.goods_product_index',$this->ViewData);
    }

    /**
     * 商品编辑与新增页面
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function GoodsView($id = 0)
    {
        /*初始化*/
        $admin_u  = session('AdminUser');
        $category = new Category();
        $goods    = new Goods();
        $this->ViewData['goods_info'] = null;
        $this->ViewData['category_tree'] = $category->getGoodsCategoryTreeCountAll($admin_u->merchant_id);

        if($id > 0)
        {
            $this->ViewData['goods_info'] = $goods->getOneGoodsRelationAll($id);
        }
        return view('admin.goods_product_edit',$this->ViewData);
    }

    /**
     * 商品 Ajax新增 提交处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function GoodsAdd(Request $request)
    {
        $admin_u  = session('AdminUser');
        $goods    = new Goods();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'goods_id'     => 'required|in:0',
            'goods_name'   => 'required',
            'goods_price'  => 'required|numeric',
            'goods_number' => 'required|integer',
            'goods_sort'   => 'required|integer',
            'goods_thumb'  => 'required|file|image',
            'category_id'     => [
                'required',
                'integer',
                Rule::exists('goods_category')->where(function ($query) {
                    $query->where('category_id',$GLOBALS['request']->input('category_id'));
                }),
            ],
            'brand_id'    => [
                'required',
                'integer',
                Rule::exists('goods_brand')->where(function ($query) use ($admin_u) {
                    $query->where([
                        ['brand_id', '=', $GLOBALS['request']->input('brand_id')],
                        ['merchant_id', '=', $admin_u->merchant_id],
                    ]);
                }),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        /*相册增加规则*/
        $validator->sometimes('goods_photo', 'array', function ($input) {
            return !empty($input->goods_photo);/*return true时才增加验证规则!*/
        });
        /*展示属性增加规则*/
        $validator->sometimes('show_attr', 'json', function ($input) {
            return !empty($input->show_attr);/*return true时才增加验证规则!*/
        });
        /*购买属性增加规则*/
        $validator->sometimes('select_attr', 'json', function ($input) {
            return !empty($input->select_attr);/*return true时才增加验证规则!*/
        });

        $request->merge(array('merchant_id' => $admin_u->merchant_id));/*将session中的merchant_id加入$request*/
        if($validator->passes() && $goods->addNormalGoods($request->all()))
        {   /*验证通过并且添加成功*/
            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failed');
            $m3result->data['validator']    = $validator->messages();
            $m3result->data['goods']        = $goods->messages();
        }

        return $m3result->toJson();
    }

    /**
     * 商品 Ajax编辑 提交处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function GoodsEdit(Request $request)
    {
        $admin_u  = session('AdminUser');
        $goods    = new Goods();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'goods_id'     => [
                'required',
                'integer',
                Rule::exists('goods_info')->where(function ($query) use($admin_u) {
                    $query->where([
                        ['goods_id', '=', $GLOBALS['request']->input('goods_id')],
                        ['merchant_id', '=', $admin_u->merchant_id],
                    ]);
                }),
            ],
            'goods_name'   => 'required',
            'goods_price'  => 'required|numeric',
            'goods_number' => 'required|integer',
            'goods_sort'   => 'required|integer',
            'category_id'     => [
                'required',
                'integer',
                Rule::exists('goods_category')->where(function ($query) {
                    $query->where('category_id',$GLOBALS['request']->input('category_id'));
                }),
            ],
            'brand_id'    => [
                'required',
                'integer',
                Rule::exists('goods_brand')->where(function ($query) use ($admin_u) {
                    $query->where([
                        ['brand_id', '=', $GLOBALS['request']->input('brand_id')],
                        ['merchant_id', '=', $admin_u->merchant_id],
                    ]);
                }),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        /*缩略图增加规则*/
        $validator->sometimes('goods_thumb', 'file|image', function ($input) use($request) {
            return $request->hasFile('goods_thumb');/*return true时才增加验证规则!*/
        });

        /*相册增加规则*/
        $validator->sometimes('goods_photo', 'array', function ($input) {
            return !empty($input->goods_photo);/*return true时才增加验证规则!*/
        });
        /*展示属性增加规则*/
        $validator->sometimes('show_attr', 'json', function ($input) {
            return !empty($input->show_attr);/*return true时才增加验证规则!*/
        });
        /*购买属性增加规则*/
        $validator->sometimes('select_attr', 'json', function ($input) {
            return !empty($input->select_attr);/*return true时才增加验证规则!*/
        });

        if($validator->passes() && $goods->editNormalGoods($request->all()))
        {   /*验证通过并且添加成功*/
            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failed');
            $m3result->data['validator']    = $validator->messages();
            $m3result->data['goods']        = $goods->messages();
        }

        return $m3result->toJson();
    }

    /**
     * 商品审核 Ajax提交
     * @param Request $request
     * @return \App\Tools\json
     */
    public function GoodsAudit(Request $request)
    {
        /*初始化*/
        $admin_u  = session('AdminUser');
        $goods    = new Goods();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'goods_id'  => [
                'required',
                'integer',
                Rule::exists('goods_info')->where(function ($query) {
                    $query->where('goods_id',$GLOBALS['request']->input('goods_id'));
                }),
            ],
            'sale_status' => [
                'required',
                Rule::in([$goods::IS_SALE,$goods::NO_SALE]),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        if($validator->passes() & $goods->auditGoodsInfo($request->input('goods_id'),$request->input('sale_status')))
        {   /*验证通过并且更新成功*/
            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failed');
            $m3result->data['validator']  = $validator->messages();
            $m3result->data['goods']      = $goods->messages();
        }
        return $m3result->toJson();
    }

    /**
     * 商品删除 Ajax提交(移入回收站)
     * @param Request $request
     * @return \App\Tools\json
     */
    public function GoodsDeleteOne(Request $request)
    {
        /*初始化*/
        $goods    = new Goods();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'goods_id'    => [
                'required',
                'integer',
                Rule::exists('goods_info')->where(function ($query) {
                    $query->where([
                        ['goods_id', '=', $GLOBALS['request']->input('goods_id')],
                        ['is_delete', '=', Goods::NO_DELETE]
                    ]);
                }),
            ]
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->passes() && $goods->deleteGoodsInfo($request->input('goods_id')))
        {   /*验证通过*/
            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failed');
            $m3result->data['validator']    = $validator->messages();
            $m3result->data['goods']        = $goods->messages();
        }
        return $m3result->toJson();
    }

    /**
     * 商品回收站列表页面(显示全部已删除的商品)
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function GoodsRecycleIndex()
    {
        /*初始化*/
        $goods     = new Goods();

        $this->ViewData['goods_list'] = array();
        $this->ViewData['nav_position']  = Menu::getAdminPosition();/*面包屑*/

        $this->ViewData['goods_list'] = $goods->getGoodsListAll([['is_delete',$goods::IS_DELETE]]);

        return view('admin.goods_recycle_index',$this->ViewData);
    }

    /**
     * 商品 Ajax销毁 提交处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function GoodsRecycleDestroyOne(Request $request)
    {
        /*初始化*/
        $goods    = new Goods();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'goods_id'    => [
                'required',
                'integer',
                Rule::exists('goods_info')->where(function ($query) {
                    $query->where([
                        ['goods_id', '=', $GLOBALS['request']->input('goods_id')],
                        ['is_delete', '=', Goods::IS_DELETE]
                    ]);
                }),
            ]
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->passes() && $goods->destroyGoodsInfo($request->input('goods_id')))
        {   /*验证通过*/
            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failed');
            $m3result->data['validator']    = $validator->messages();
            $m3result->data['goods']        = $goods->messages();
        }
        return $m3result->toJson();
    }

    /**
     * 商品 Ajax恢复 提交处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function GoodsRecycleRecovery(Request $request)
    {
        /*初始化*/
        $goods    = new Goods();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'goods_id'    => [
                'required',
                'integer',
                Rule::exists('goods_info')->where(function ($query) {
                    $query->where([
                        ['goods_id', '=', $GLOBALS['request']->input('goods_id')],
                        ['is_delete', '=', Goods::IS_DELETE]
                    ]);
                }),
            ]
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->passes() && $goods->recoveryGoodsInfo($request->input('goods_id')))
        {   /*验证通过*/
            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failed');
            $m3result->data['validator']    = $validator->messages();
            $m3result->data['goods']        = $goods->messages();
        }
        return $m3result->toJson();
    }
}