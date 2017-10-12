<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/18 0018
 * Time  : 14:17
 */

namespace App\Models;
use App\Entity\GoodsCategory;
use Illuminate\Support\Facades\DB;

/**
 * Class Category 商品分类的模型
 * @package App\Models
 */
class Category extends CommonModel{

    private $errors =array(); /*错误信息*/

    /**
     * 获取所有商品分类,用于zTree的未分级格式 (已转换中英文,传入标识位数量统计)
     * @param string $count 需要统计的下级数量
     * @return mixed
     */
    public function getGoodsCategoryTree($count = 'goods')
    {
        /*初始化*/
        $goods_category = new GoodsCategory();
        $relation = '';

        switch($count)
        {
            case 'goods':
                $relation = ['hm_goods_info' => function ($query) {
                    $query->where('is_delete' , Goods::NO_DELETE);
                }];
                break;
            case 'brand':
                $relation = 'hm_goods_brand';
                break;
            case 'attr':
                $relation = 'hm_goods_attributes';
                break;
        }
        $data = $goods_category->withCount($relation)->orderBy('category_sort', 'asc')->get();

        $data->transform(function($item) use ($relation,$count)
        {
            switch($count)
            {
                case 'goods':
                    $item->my_count = array_pull($item,'hm_goods_info_count');
                    break;
                case 'brand':
                    $item->my_count = array_pull($item,'hm_goods_brand_count');
                    break;
                case 'attr':
                    $item->my_count = array_pull($item,'hm_goods_attributes_count');
                    break;
            }
            $item->name = CommonModel::languageFormat($item->category_name , $item->category_en_name);
            $item->alias = CommonModel::languageFormat($item->alias_name , $item->alias_en_name);
            return $item;
        });
        return $data;
    }


    /**
     * 获取所有商品分类,统计分类下全部类型数量,用于zTree的未分级格式 (已转换中英文,传入merchant_id则统计对应 id 的数量)
     * @param null $merchant_id
     * @return mixed
     */
    public function getGoodsCategoryTreeCountAll($merchant_id = null)
    {
        /*初始化*/
        $goods_category = new GoodsCategory();

        if($merchant_id === null)
        {
            $data = $goods_category->withCount('hm_goods_info','hm_goods_brand','hm_goods_attributes')->orderBy('category_sort', 'asc')->get();
        }
        else if($merchant_id >= 0)
        {
            $data = $goods_category->withCount([
                'hm_goods_info' => function ($query) use($merchant_id) {
                    $query->where('merchant_id' , $merchant_id);
                },
                'hm_goods_brand' => function ($query)use($merchant_id) {
                    $query->where('merchant_id' , $merchant_id);
                },
                'hm_goods_attributes'
            ])->orderBy('category_sort', 'asc')->get();
        }

        $data->transform(function($item)
        {
            $item->count_desc = " | 商($item->hm_goods_info_count) 牌($item->hm_goods_brand_count) 属($item->hm_goods_attributes_count)";
            $item->name = CommonModel::languageFormat($item->category_name , $item->category_en_name);
            $item->alias = CommonModel::languageFormat($item->alias_name , $item->alias_en_name);
            return $item;
        });

        return $data;
    }

    /**
     * 添加一个商品分类
     * @param $request
     * @return bool
     */
    public function addGoodsCategory($request)
    {
        /*初始化*/
        $goods_category = new GoodsCategory();

        $goods_category->parent_id = $request->input('parent_id');
        $goods_category->category_name = $request->input('category_name');
        $goods_category->category_en_name = $request->has('category_en_name') ? $request->input('category_en_name') : '';
        $goods_category->alias_name = $request->input('alias_name');
        $goods_category->alias_en_name = $request->has('alias_en_name') ? $request->input('alias_en_name') : '';
        $goods_category->category_sort = $request->input('category_sort');
        $goods_category->save();
        Rbac::adminLog('新增商品分类:'.$goods_category->category_name."($goods_category->category_id)");

        return true;
    }

    /**
     * 更新一个商品分类
     * @param $request
     * @return bool
     */
    public function editGoodsCategory($request)
    {
        /*初始化*/
        $goods_category = new GoodsCategory();
        $edit_category    = $goods_category->findOrFail($request->input('category_id'));
        $name             = $edit_category->category_name;

        $edit_category->parent_id = $request->input('parent_id');
        $edit_category->category_name = $request->input('category_name');
        $edit_category->category_en_name = $request->has('category_en_name') ? $request->input('category_en_name') : '';
        $edit_category->alias_name = $request->input('alias_name');
        $edit_category->alias_en_name = $request->has('alias_en_name') ? $request->input('alias_en_name') : '';
        $edit_category->category_sort = $request->input('category_sort');
        $edit_category->save();
        Rbac::adminLog('编辑商品分类:'.$name."($edit_category->category_id)");

        return true;
    }

    /**
     * 删除一个商品分类
     * @param $category_id
     * @return bool
     */
    public function deleteGoodsCategory($category_id)
    {
        /*初始化*/
        $goods_category = new GoodsCategory();
        $child_category  = null;

        $delete_category = $this->getOneCategoryRelationBrand($category_id);
        $child_category  = $goods_category->where('parent_id',$category_id)->get();

        if(!$child_category->isEmpty())
        {
            $this->errors['code'] = 1;
            $this->errors['messages'] = __('admin.failed').',当前分类下存在下级分类';
            return false;
        }
        elseif(!$delete_category->goods_brand->isEmpty())
        {
            $this->errors['code'] = 2;
            $this->errors['messages'] = __('admin.failed').',当前分类下存在商品品牌';
            return false;
        }
        else
        {
            $delete_category->delete();
            Rbac::adminLog('删除商品分类:'.$delete_category->category_name."($delete_category->category_id)");
            return true;
        }
    }

    /**
     * 获取单个商品分类数据关联分类下品牌(转换中英文)
     * @param null $category_id
     * @return bool
     */
    public function getOneCategoryRelationBrand($category_id = null)
    {
        /*初始化*/
        $goods_category  =  new GoodsCategory();

        if($category_id)
        {
            /*查询*/
            $data = $goods_category->findOrFail($category_id);
            /*数据过滤*/
            $data->name = CommonModel::languageFormat($data->category_name,$data->category_en_name);
            $data->alias= CommonModel::languageFormat($data->alias_name,$data->alias_en_name);
            $data->goods_brand = $data->hm_goods_brand;
            return $data;
        }
        else
        {
            return false;
        }
    }

    /**
     * 获取单个商品分类数据关联分类下商品属性(转换中英文)
     * @param null $category_id
     * @return bool
     */
    public function getOneCategoryRelationAttributes($category_id = null)
    {
        /*初始化*/
        $goods_category  =  new GoodsCategory();
        if($category_id)
        {
            /*查询*/
            $data = $goods_category->findOrFail($category_id);
            /*数据过滤*/
            $data->name = CommonModel::languageFormat($data->category_name,$data->category_en_name);
            $data->alias= CommonModel::languageFormat($data->alias_name,$data->alias_en_name);
            $data->goods_attributes = $data->hm_goods_attributes;
            return $data;
        }
        else
        {
            return false;
        }
    }

    /**
     * 获取商品添加时分类对应的相关数据
     * type格式 'attr,brand'
     * @param $arr *category_id *type  *merchant_id
     * @return bool|null
     */
    public function getGoodsAddRelationCategory($arr)
    {
        /*初始化*/
        $goods_category = GoodsCategory::findOrFail($arr['category_id']);
        $data  = null;

        if(!array_has($arr, 'merchant_id'))
        {
            $this->errors['code'] = 1;
            $this->errors['messages'] = '未找到必要的参数 merchant_id';
            return false;
        }

        $type_arr = explode(',',$arr['type']);
        foreach($type_arr as $item)
        {
            switch($item)
            {
                case 'attr':
                    $data['goods_attributes'] = $goods_category->hm_goods_attributes;
                    break;
                case 'brand':
                    $data['goods_brand'] = $goods_category->hm_goods_brand()->where('merchant_id',$arr['merchant_id'])->get();
                    break;
            }
        }

        if(empty($data))
        {
            return false;
        }
        else
        {
            return $data;
        }
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