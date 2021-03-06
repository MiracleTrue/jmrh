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
use Illuminate\Support\Facades\Log;

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
     * 检查产品及规格有效性
     * @param $product_name
     * @param $spec_name
     * @return bool || 查找到的产品数据
     */
    public static function checkProduct($product_name, $spec_name)
    {
        /*初始化*/
        $e_products = Products::where('product_name', $product_name)->first();
        if (empty($e_products))
        {
            return false;
        }
        $e_products->spec_info = ProductSpec::where('product_id', $e_products->product_id)->where('spec_name', $spec_name)->first();
        if (!empty($e_products->spec_info))
        {
            return $e_products;
        }
        return false;
    }

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
            $query->orderBy('products.sort', 'desc');
        }])
            ->where('product_category.is_index', self::CATEGORY_IS_INDEX)
            ->orderBy('product_category.sort', 'desc')
            ->get();

        /*数据过滤*/
        $list->transform(function ($item)
        {
            $item->products = $item->hm_products;
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
        $e_products = $e_products->with('ho_product_category', 'hm_product_spec')
            ->where($where);
        foreach ($orderBy as $value)
        {
            $e_products->orderBy($value[0], $value[1]);
        }
        /*是否需要分页数据*/
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
                $this->deleteProduct($item->product_id);
                header("location: " . action('ProductController@ProductList'));
            }
            else
            {
                $item->product_category = $item->ho_product_category;
                $item->product_spec = $item->hm_product_spec;
            }
            unset($item->ho_product_category);
            unset($item->hm_product_spec);
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
        $e_product_category = $e_product_category->withCount('hm_products')->with('ho_users')
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
            $item->manage_user = $item->ho_users;
            unset($item->hm_products_count);
            unset($item->ho_users);
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
        $e_product_category = $e_product_category->withCount('hm_products')->get();
        /*排序,去重,限数,分割返回新集合*/
        $unit_list = $e_product_category->sortByDesc('hm_products_count')->unique('unit')->take($number)->pluck('unit');
        return $unit_list;
    }

    /**
     * 获取单个商品 (已关联: 分类,规格,协议价) (已转换:缩略图路径, 原图路径)
     * @param $product_id
     * @return mixed
     */
    public function getProductInfo($product_id)
    {
        /*初始化*/
        $e_products = Products::where('product_id', $product_id)->first() or die();
        $e_products->product_thumb = MyFile::makeUrl($e_products->product_thumb);
        $e_products->category_info = $e_products->ho_product_category;
        $e_products->spec_info = ProductSpec::where('product_id', $e_products->product_id)->with('hm_supplier_price')->get();

        /*数据过滤*/
        $e_products->spec_info->transform(function ($item)
        {
            $item->de_image_thumb = $item->image_thumb;
            $item->de_image_original = $item->image_original;
            $item->image_thumb = MyFile::makeUrl($item->image_thumb);
            $item->image_original = MyFile::makeUrl($item->image_original);
            $item->supplier_price = $item->hm_supplier_price;
            unset($item->hm_supplier_price);
            return $item;
        });
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
        $e_product_category = ProductCategory::where('category_id', $id)->first() or die();
        $e_product_category->manage_user = $e_product_category->ho_users;

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
        $e_product_category->manage_user_id = !empty($arr['manage_user_id']) ? $arr['manage_user_id'] : null;
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
        $e_product_category->manage_user_id = !empty($arr['manage_user_id']) ? $arr['manage_user_id'] : null;


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
        /*事物*/
        try
        {
            DB::transaction(function () use ($id)
            {
                $e_product_category = ProductCategory::find($id);
                /*伪删除*/
                $e_product_category->is_delete = self::CATEGORY_IS_DELETE;
                $e_product_category->save();
                /*删除下级商品*/
                $e_product = Products::where('category_id', $id)->get();

                $e_product->each(function ($item)
                {
                    $this->deleteProduct($item->product_id);
                });
                User::userLog($e_product_category->category_name . "(计量单位:$e_product_category->unit)");
            });
        } catch (\Exception $e)
        {
            $this->errors['code'] = 1;
            $this->errors['messages'] = '分类删除失败';
            return false;
        }

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

        /* 去重规格名和供应商id*/
        $collection = collect(json_decode($arr['spec_json'], true))->unique('spec_name');
        $collection->transform(function ($item)
        {
            $item['supplier_price'] = collect($item['supplier_price'])->unique('user_id')->values()->all();
            return $item;
        });
        $spec_json_arr = $collection->values()->all();

        /*添加商品*/
        $e_products->category_id = !empty($arr['category_id']) ? $arr['category_id'] : 0;
        $e_products->product_name = !empty($arr['product_name']) ? $arr['product_name'] : '';
        $e_products->product_thumb = request()->hasFile('product_thumb') ? $my_file->uploadThumb(request('product_thumb')) : $arr['product_thumb'];
        $e_products->product_content = !empty($arr['product_content']) ? $arr['product_content'] : '';
        $e_products->sort = !empty($arr['sort']) ? $arr['sort'] : 0;
        $e_products->create_time = Carbon::now()->timestamp;
        $e_products->is_delete = self::PRODUCT_NO_DELETE;

        /*事物*/
        try
        {
            DB::transaction(function () use ($arr, $e_products, $spec_json_arr)
            {
                $e_products->save();
                foreach ($spec_json_arr as $spec_key => $spec)
                {
                    $spec['product_id'] = $e_products->product_id;
                    $create_spec = $this->addSpec($spec);
                    foreach ($spec['supplier_price'] as $price)
                    {
                        $price['spec_id'] = $create_spec->spec_id;
                        $this->addSupplierPrice($price);
                    }
                }
                User::userLog($e_products->product_name . "(商品分类:" . ProductCategory::find($e_products->category_id)->category_name . ")");
            });
        } catch (\Exception $e)
        {
            $this->errors['code'] = 2;
            $this->errors['messages'] = '网络繁忙';
            return false;
        }

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

        /* 去重规格名和供应商id*/
        $collection = collect(json_decode($arr['spec_json'], true))->unique('spec_name');
        $collection->transform(function ($item)
        {
            $item['supplier_price'] = collect($item['supplier_price'])->unique('user_id')->values()->all();
            return $item;
        });
        $spec_json_arr = $collection->values()->all();

        /*事物*/
        try
        {
            DB::transaction(function () use ($arr, $e_products, $spec_json_arr, $my_file)
            {
                /*修改*/
                $e_products = Products::lockForUpdate()->find($arr['product_id']);
                $e_products->category_id = !empty($arr['category_id']) ? $arr['category_id'] : 0;
                $e_products->product_name = !empty($arr['product_name']) ? $arr['product_name'] : '';
                if (request()->hasFile('product_thumb'))
                {
                    $e_products->product_thumb = $my_file->uploadThumb(request('product_thumb'));
                    $e_products->product_original = $my_file->uploadOriginal(request('product_thumb'));
                }
                $e_products->product_content = !empty($arr['product_content']) ? $arr['product_content'] : '';
                $e_products->sort = !empty($arr['sort']) ? $arr['sort'] : 0;
                $e_products->save();
                /*如与数据库中数据不一致,删除所有规格及协议价,进行更新*/
                $this->deleteProductSpecAndSupplierPrice($e_products->product_id);
                foreach ($spec_json_arr as $spec_key => $spec)
                {
                    $spec['product_id'] = $e_products->product_id;
                    $create_spec = $this->addSpec($spec);
                    foreach ($spec['supplier_price'] as $price)
                    {
                        $price['spec_id'] = $create_spec->spec_id;
                        $this->addSupplierPrice($price);
                    }
                }
                User::userLog($e_products->product_name . "(商品分类:" . ProductCategory::find($e_products->category_id)->category_name . ")");
            });
        } catch (\Exception $e)
        {
            $this->errors['code'] = 2;
            $this->errors['messages'] = '网络繁忙';
            return false;
        }

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
        /*新增*/
        $e_product_spec->product_id = $arr['product_id'];
        $e_product_spec->spec_name = !empty($arr['spec_name']) ? $arr['spec_name'] : '';
        $e_product_spec->spec_unit = !empty($arr['spec_unit']) ? $arr['spec_unit'] : '';
        $e_product_spec->product_price = !empty($arr['product_price']) ? $arr['product_price'] : 0;
        $e_product_spec->image_thumb = !empty($arr['image_thumb']) ? $arr['image_thumb'] : '';
        $e_product_spec->image_original = !empty($arr['image_original']) ? $arr['image_original'] : '';

        $e_product_spec->save();
        return $e_product_spec;
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

        return $e_supplier_price;
    }

    /**
     * 删除单个商品所有规格与供应商协议价
     * @param $product_id
     */
    public function deleteProductSpecAndSupplierPrice($product_id)
    {
        /*初始化*/
        $my_file = new MyFile();

        $product_spec = ProductSpec::where('product_id', $product_id)->get();
        $spec_ids = $product_spec->pluck('spec_id')->all();

        /*删除图片文件*/
        $product_spec->each(function ($item) use ($my_file)
        {
            $my_file->deleteFile($item->image_thumb);
            $my_file->deleteFile($item->image_original);
        });
        /*删除数据*/
        SupplierPrice::whereIn('spec_id', $spec_ids)->delete();
        ProductSpec::where('product_id', $product_id)->delete();
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
        $name = $e_products->product_name;
        $e_products->product_name = '已删除-' . $name;
        $e_products->is_delete = self::PRODUCT_IS_DELETE;

        $e_products->save();
        User::userLog($name . "(商品分类:" . ProductCategory::withoutGlobalScope('is_delete')->find($e_products->category_id)->category_name . ")");
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