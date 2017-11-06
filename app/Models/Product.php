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


    private $errors = array(); /*错误信息*/

    /**
     * 获取所有商品列表 (已关联: 分类) (如有where 则加入新的sql条件) "分页" | 默认排序:排序值
     * @param array $where
     * @param array $orderBy
     * @return mixed
     */
    public function getProductList($where = array(), $orderBy = array(['products.sort', 'desc']))
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
        $product_list = $e_products->paginate($_COOKIE['PaginationSize']);

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
        }])
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
            $item->labels = explode(',',$item->labels);
            unset($item->hm_products_count);
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
     * 获取单个商品 (已转换:缩略图路径, 原图路径)
     * @param $id
     * @return mixed
     */
    public function getProductInfo($id)
    {
        /*初始化*/
        $e_products = Products::where('product_id', $id)->where('is_delete', self::PRODUCT_NO_DELETE)->first() or die();
        $e_products->product_original = MyFile::makeUrl($e_products->product_original);
        $e_products->product_thumb = MyFile::makeUrl($e_products->product_thumb);
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
        $e_product_category->labels = !empty($arr['labels']) ? $arr['labels'] : '';
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
        $e_product_category->labels = !empty($arr['labels']) ? $arr['labels'] : '';


        $e_product_category->save();
        User::userLog($e_product_category->category_name . "(计量单位:$e_product_category->unit)");
        return true;
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
        $e_products->product_thumb = request()->hasFile('product_image') ? $my_file->uploadThumb(request('product_image')) : $arr['product_image'];
        $e_products->product_original = request()->hasFile('product_image') ? $my_file->uploadOriginal(request('product_image')) : $arr['product_image'];
        $e_products->product_content = !empty($arr['product_content']) ? $arr['product_content'] : '';
        $e_products->sort = !empty($arr['sort']) ? $arr['sort'] : 0;
        $e_products->create_time = Carbon::now()->timestamp;
        $e_products->is_delete = self::PRODUCT_NO_DELETE;

        $e_products->save();
        User::userLog($e_products->product_name . "(商品分类:" . ProductCategory::find($e_products->category_id)->category_name . ")");
        return true;
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


    /**
     * 返回 模型 发生的错误信息
     * @return mixed
     */
    public function messages()
    {
        return $this->errors;
    }
}