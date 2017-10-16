<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 13:38
 */

namespace App\Models;

use App\Entity\ProductCategory;
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


    private $errors = array(); /*错误信息*/

    /**
     * 获取所有商品分类列表 (已统计: 商品数量) (如有where 则加入新的sql条件) "分页" | 默认排序:排序值
     * @param array $where
     * @param array $orderBy
     * @return mixed
     */
    public function getProductCategoryList($where = array(), $orderBy = array(['product_category.sort', 'desc']))
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
        $category_list = $e_product_category->paginate($_COOKIE['PaginationSize']);

        /*数据过滤*/
        $category_list->transform(function($item)
        {
            $item->product_count = $item->hm_products_count;
            unset($item->hm_products_count);
            return $item;
        });
        return $category_list->toArray();
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

        return $e_product_category->toArray();
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