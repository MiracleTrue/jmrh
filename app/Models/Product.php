<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 13:38
 */

namespace App\Models;

use App\Entity\ProductCategory;
use App\Entity\Products;
use App\Entity\ProductsCategoryManage;
use App\Entity\ProductSpec;
use App\Entity\SupplierPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


/**
 * Class Product 产品相关模型
 * @package App\Models
 */
class Product extends CommonModel
{
    /*商品分类删除状态:  1.删除  0.正常*/
    const CATEGORY_IS_DELETE = 1;
    const CATEGORY_NO_DELETE = 0;
    /*商品删除状态:  1.删除  0.正常*/
    const PRODUCT_IS_DELETE = 1;
    const PRODUCT_NO_DELETE = 0;
    /*商品分类是否首页显示 1.显示  0.不显示*/
    const  CATEGORY_IS_INDEX = 1;
    const  CATEGORY_NO_INDEX = 0;

    /**
     * 获取首页商品分类楼层 (已关联: 商品) 默认排序:排序值
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getWelcomeProductList()
    {
        /*初始化*/
        $e_product_category = new ProductCategory();

        $list = $e_product_category->with(['hm_products' => function ($query)
        {
            $query->where('products.is_delete', self::CATEGORY_NO_DELETE)->orderBy('products.sort', 'desc');
        }])
            ->where('product_category.is_index', self::CATEGORY_IS_INDEX)
            ->where('product_category.is_delete', self::CATEGORY_NO_DELETE)
            ->orderBy('product_category.sort', 'desc')
            ->get();

        /*数据过滤*/
        $list->transform(function ($item)
        {
            $item->products = $item->hm_products->take(10);
            return $item;
        });

        return $list;
    }

    /**
     * 获取所有商品列表 (已关联: 分类) (如有where 则加入新的sql条件) "分页" | 默认排序:排序值
     * @param array $where
     * @param array $orderBy
     * @param bool $is_paginate & 是否需要分页
     * @return mixed
     */
    public function getProductList($where = array(), $orderBy = array(['products.sort', 'desc']), $is_paginate = true)
    {
        /*初始化*/
        $e_products = new Products();

        /*预加载ORM对象*/
        $e_products = $e_products->with(['ho_product_category' => function ($query)
        {
            $query->where('product_category.is_delete', self::CATEGORY_NO_DELETE);
        }])
            ->where('products.is_delete', self::PRODUCT_NO_DELETE)
            ->where($where);
        foreach ($orderBy as $value)
        {
            $e_products->orderBy($value[0], $value[1]);
        }
        if ($is_paginate === true)
        {
            $product_list = $e_products->paginate($_COOKIE['PaginationSize']);
        }
        else
        {
            $product_list = $e_products->get();
        }

        /*数据过滤*/
        $product_list->transform(function ($item)
        {
            if (empty($item->ho_product_category))
            {   /*如果是无效的分类,将产品删除*/
                $item_delete = Products::find($item->product_id);
                $item_delete->is_delete = Product::PRODUCT_IS_DELETE;
                $item_delete->save();
                header("location: " . action('ProductController@ProductList'));
            }
            else
            {
                $item->product_category = $item->ho_product_category->toArray();
            }
            unset($item->ho_product_category);
            return $item;
        });
        return $product_list;
    }

    /**
     * 获取所有商品分类列表 (已统计: 商品数量) (如有where 则加入新的sql条件) | 默认排序:排序值
     * @param array $where
     * @param array $orderBy
     * @param bool $is_paginate & 是否需要分页
     * @return mixed
     */
    public function getProductCategoryList($where = array(), $orderBy = array(['product_category.sort', 'desc']), $is_paginate = true)
    {
        /*初始化*/
        $e_product_category = new ProductCategory();

        /*预加载ORM对象*/
        $e_product_category = $e_product_category->withCount(['hm_products' => function ($query)
        {
            $query->where('products.is_delete', self::PRODUCT_NO_DELETE);
        }])->with('hmt_users')
            ->where('product_category.is_delete', self::CATEGORY_NO_DELETE)
            ->where($where);
        foreach ($orderBy as $value)
        {
            $e_product_category->orderBy($value[0], $value[1]);
        }
        if ($is_paginate === true)
        {
            $category_list = $e_product_category->paginate($_COOKIE['PaginationSize']);
        }
        else
        {
            $category_list = $e_product_category->get();
        }

        /*数据过滤*/
        $category_list->transform(function ($item)
        {
            $item->product_count = $item->hm_products_count;
            $item->manage_user = $item->hmt_users->first();
            unset($item->hm_products_count);
            unset($item->hmt_users);
            return $item;
        });
        return $category_list;
    }

    /**
     * 获取所有分类计量单位列表 | 默认排序:商品数量
     * @param int $number 指定返回几条
     * @return mixed
     */
    public function getProductCategoryUnitList($number = 10)
    {
        /*初始化*/
        $e_product_category = new ProductCategory();
        /*预加载ORM对象*/
        $e_product_category = $e_product_category->withCount(['hm_products' => function ($query)
        {
            $query->where('products.is_delete', self::PRODUCT_NO_DELETE);
        }])->where('product_category.is_delete', self::CATEGORY_NO_DELETE)->get();
        /*排序,去重,限数,分割返回新集合*/
        $unit_list = $e_product_category->sortByDesc('hm_products_count')->unique('unit')->take($number)->pluck('unit');
        return $unit_list;
    }

    /**
     * 获取单个商品 (已关联: 分类) (已转换:缩略图路径, 原图路径)
     * @param $id
     * @return mixed
     */
    public function getProductInfo($id)
    {
        /*初始化*/
        $e_products = Products::where('product_id', $id)->where('is_delete', self::PRODUCT_NO_DELETE)->first() or die();
        $e_products->product_original = MyFile::makeUrl($e_products->product_original);
        $e_products->product_thumb = MyFile::makeUrl($e_products->product_thumb);
        $e_products->category_info = $e_products->ho_product_category;
        return $e_products;
    }

    /**
     * 获取单个商品分类
     * @param $id
     * @return mixed
     */
    public function getProductCategory($id)
    {
        /*初始化*/
        $e_product_category = ProductCategory::where('category_id', $id)->where('is_delete', self::CATEGORY_NO_DELETE)->first() or die();

        return $e_product_category;
    }

    /**
     * 添加单个商品分类
     * @param $arr
     * @return bool
     */
    public function addProductCategory($arr)
    {
        /*初始化*/
        $e_product_category = new ProductCategory();

        /*添加*/
        $e_product_category->category_name = !empty($arr['category_name']) ? $arr['category_name'] : '';
        $e_product_category->unit = !empty($arr['unit']) ? $arr['unit'] : '';
        $e_product_category->sort = !empty($arr['sort']) ? $arr['sort'] : '';
        $e_product_category->is_delete = self::CATEGORY_NO_DELETE;
        $e_product_category->is_index = self::CATEGORY_NO_INDEX;

        $e_product_category->save();
        User::userLog($e_product_category->category_name . "(计量单位:$e_product_category->unit)");
        return true;
    }

    /**
     * 修改单个商品分类
     * @param $arr
     * @return bool
     */
    public function editProductCategory($arr)
    {
        /*初始化*/
        $e_product_category = ProductCategory::find($arr['category_id']);

        /*修改*/
        $e_product_category->category_name = !empty($arr['category_name']) ? $arr['category_name'] : '';
        $e_product_category->unit = !empty($arr['unit']) ? $arr['unit'] : '';
        $e_product_category->sort = !empty($arr['sort']) ? $arr['sort'] : '';

        $e_product_category->save();
        User::userLog($e_product_category->category_name . "(计量单位:$e_product_category->unit)");
        return true;
    }

    /**
     * 平台运营员分配分类的负责人
     * @param $user_id
     * @param $category_arr [1,8,15]
     * @return bool
     * @throws \Exception
     */
    public function platformAdminShareProductCategory($user_id, $category_arr)
    {
        $category_arr = array_flatten($category_arr);
        /*事物*/
        try
        {
            DB::transaction(function () use ($user_id, $category_arr)
            {
                ProductsCategoryManage::where('user_id', $user_id)->delete();
                if (ProductsCategoryManage::whereIn('category_id', $category_arr)->get()->isNotEmpty())
                {
                    throw new \Exception(1);/*已有负责人*/
                }
                foreach ($category_arr as $value)
                {
                    $item = new ProductsCategoryManage();
                    $item->user_id = $user_id;
                    $item->category_id = $value;
                    $item->save();
                }
                return true;
            });
        } catch (\Exception $e)
        {
            $this->errors['code'] = 1;
            $this->errors['messages'] = '负责分类分配不正确或已有负责人';
            throw new \Exception(1);/*已有负责人*/
        }
        return false;
    }

    /**
     * 商品分类首页显示开关
     * @param $id
     * @return bool
     */
    public function isIndexProductCategory($id)
    {
        /*初始化*/
        $e_product_category = ProductCategory::find($id);
        /*修改*/
        if ($e_product_category->is_index == self::CATEGORY_IS_INDEX)
        {
            $e_product_category->is_index = self::CATEGORY_NO_INDEX;
        }
        else
        {
            $e_product_category->is_index = self::CATEGORY_IS_INDEX;
        }

        $e_product_category->save();
        User::userLog($e_product_category->category_name);
        return true;
    }

    /**
     * 删除单个商品分类 (伪删除)
     * @param $id
     * @return bool
     */
    public function deleteProductCategory($id)
    {
        /*初始化*/
        $e_product_category = ProductCategory::find($id);
        /*伪删除*/
        $e_product_category->is_delete = self::CATEGORY_IS_DELETE;

        $e_product_category->save();
        User::userLog($e_product_category->category_name . "(计量单位:$e_product_category->unit)");
        return true;
    }

    /**
     * 添加单个商品
     * @param $arr
     * @return bool
     */
    public function addProduct($arr)
    {
        /*初始化*/
        $e_products = new Products();
        $my_file = new MyFile();

        /*添加*/
        $e_products->category_id = !empty($arr['category_id']) ? $arr['category_id'] : 0;
        $e_products->product_name = !empty($arr['product_name']) ? $arr['product_name'] : '';
        $e_products->product_unit = !empty($arr['product_unit']) ? $arr['product_unit'] : '';
        $e_products->product_thumb = request()->hasFile('product_thumb') ? $my_file->uploadThumb(request('product_thumb')) : $arr['product_thumb'];
        $e_products->product_content = !empty($arr['product_content']) ? $arr['product_content'] : '';
        $e_products->sort = !empty($arr['sort']) ? $arr['sort'] : 0;
        $e_products->create_time = Carbon::now()->timestamp;
        $e_products->is_delete = self::PRODUCT_NO_DELETE;

        $e_products->save();
        User::userLog($e_products->product_name . "(商品分类:" . ProductCategory::find($e_products->category_id)->category_name . ")");
        return $e_products;
    }

    /**
     * 修改单个商品
     * @param $arr
     * @return bool
     */
    public function editProduct($arr)
    {
        /*初始化*/
        $e_products = Products::find($arr['product_id']);
        $my_file = new MyFile();

        /*修改*/
        $e_products->category_id = !empty($arr['category_id']) ? $arr['category_id'] : 0;
        $e_products->product_name = !empty($arr['product_name']) ? $arr['product_name'] : '';
        $e_products->product_price = !empty($arr['product_price']) ? $arr['product_price'] : 0;
        $e_products->product_unit = !empty($arr['product_unit']) ? $arr['product_unit'] : '';
        if (request()->hasFile('product_image'))
        {
            $e_products->product_thumb = $my_file->uploadThumb(request('product_image'));
            $e_products->product_original = $my_file->uploadOriginal(request('product_image'));
        }
        $e_products->product_content = !empty($arr['product_content']) ? $arr['product_content'] : '';
        $e_products->sort = !empty($arr['sort']) ? $arr['sort'] : 0;

        $e_products->save();
        User::userLog($e_products->product_name . "(商品分类:" . ProductCategory::find($e_products->category_id)->category_name . ")");
        return true;
    }

    /**
     * 删除单个商品 (伪删除)
     * @param $id
     * @return bool
     */
    public function deleteProduct($id)
    {
        /*初始化*/
        $e_products = Products::find($id);
        /*伪删除*/
        $e_products->is_delete = self::PRODUCT_IS_DELETE;

        $e_products->save();
        User::userLog($e_products->product_name . "(商品分类:" . ProductCategory::find($e_products->category_id)->category_name . ")");
        return true;
    }

    /**
     * 新增一个商品规格
     * @param $arr
     * @return ProductSpec
     */
    public function addSpec($arr)
    {
        /*初始化*/
        $e_product_spec = new ProductSpec();
        $my_file = new MyFile();
        /*新增*/
        $e_product_spec->product_id = $arr['product_id'];
        $e_product_spec->spec_name = !empty($arr['spec_name']) ? $arr['spec_name'] : '';
        $e_product_spec->product_price = !empty($arr['product_price']) ? $arr['product_price'] : 0;
        $e_product_spec->image_thumb = $my_file->uploadThumb(request('spec_image'));
        $e_product_spec->image_original = $my_file->uploadOriginal(request('spec_image'));

        $e_product_spec->save();
        User::userLog($e_product_spec->spec_name);
        return $e_product_spec;
    }

    /**
     * 修改单个商品规格
     * @param $arr
     * @return bool
     */
    public function editSpec($arr)
    {
        /*初始化*/
        $e_product_spec = ProductSpec::find($arr['spec_id']);
        $my_file = new MyFile();

        /*修改*/
        $e_product_spec->spec_name = !empty($arr['spec_name']) ? $arr['spec_name'] : '';
        $e_product_spec->product_price = !empty($arr['product_price']) ? $arr['product_price'] : 0;
        if (request()->hasFile('spec_image'))
        {
            $e_product_spec->image_thumb = $my_file->uploadThumb(request('spec_image'));
            $e_product_spec->image_original = $my_file->uploadOriginal(request('spec_image'));
        }
        $e_product_spec->save();
        User::userLog($e_product_spec->spec_name);
        return true;
    }

    /**
     * 删除单个商品规格
     * @param $spec_id
     * @return bool
     */
    public function deleteSpec($spec_id)
    {
        /*初始化*/
        $e_product_spec = ProductSpec::find($spec_id);
        $my_file = new MyFile();

        /*删除图片文件 并且 删除数据*/
        $my_file->deleteFile($e_product_spec->image_thumb);
        $my_file->deleteFile($e_product_spec->image_original);
        $e_product_spec->delete();
        User::userLog($e_product_spec->spec_name);
        return true;
    }

    /**
     * 获取单个规格的所有供货商协议价
     * @param $spec_id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getSpecSupplierPrice($spec_id)
    {
        /*初始化*/
        $e_supplier_price = new SupplierPrice();

        $list = $e_supplier_price->with(['ho_users' => function ($query)
        {
            $query->where('users.is_disable', User::NO_DISABLE);
        }])
            ->where('supplier_price.price_id', $spec_id)
            ->get();

        /*数据过滤*/
//        $list->transform(function ($item)
//        {
//            $item->products = $item->hm_products->take(10);
//            $item->labels = explode(',', $item->labels);
//            return $item;
//        });

        return $list;
    }

    /**
     * 新增一个供应商协议价
     * @param $arr
     * @return bool
     */
    public function addSupplierPrice($arr)
    {
        /*初始化*/
        $e_supplier_price = new SupplierPrice();

        /*新增*/
        $e_supplier_price->user_id = $arr['user_id'];
        $e_supplier_price->spec_id = $arr['spec_id'];
        $e_supplier_price->price = !empty($arr['price']) ? $arr['price'] : 0;
        $e_supplier_price->save();
        User::userLog("供应商ID:" . $e_supplier_price->user_id . " 规格ID:" . $e_supplier_price->spec_id);

        return $e_supplier_price;
    }

    /**
     * 修改一个供应商协议价
     * @param $arr
     * @return bool
     */
    public function editSupplierPrice($arr)
    {
        /*初始化*/
        $e_supplier_price = SupplierPrice::find($arr['price_id']);

        /*修改*/
        $e_supplier_price->user_id = $arr['user_id'];
        $e_supplier_price->price = !empty($arr['price']) ? $arr['price'] : 0;
        $e_supplier_price->save();
        User::userLog("供应商ID:" . $e_supplier_price->user_id . " 规格ID:" . $e_supplier_price->spec_id);

        return $e_supplier_price;
    }

    /**
     * 删除一个供应商协议价
     * @param $price_id
     * @return bool
     */
    public function deleteSupplierPrice($price_id)
    {
        /*初始化*/
        $e_supplier_price = SupplierPrice::find($price_id);
        $e_supplier_price->delete();
        User::userLog("供应商协议价ID:" . $e_supplier_price->price_id);

        return true;
    }

    /**
     * 返回 商品分类 删除状态的文本名称
     * @param $status
     * @return string
     */
    public static function categoryDeleteTransformText($status)
    {
        $text = '';
        switch ($status)
        {
            case self::CATEGORY_IS_DELETE:
                $text = '已删除';
                break;
            case self::CATEGORY_NO_DELETE:
                $text = '正常';
                break;
        }
        return $text;
    }

    /**
     * 返回 商品 删除状态的文本名称
     * @param $status
     * @return string
     */
    public static function productDeleteTransformText($status)
    {
        $text = '';
        switch ($status)
        {
            case self::CATEGORY_IS_DELETE:
                $text = '已删除';
                break;
            case self::CATEGORY_NO_DELETE:
                $text = '正常';
                break;
        }
        return $text;
    }
}