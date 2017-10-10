<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/18 0018
 * Time  : 14:17
 */

namespace App\Models;
use App\Entity\GoodsBrand;

/**
 * Class Brand 商品品牌的模型
 * @package App\Models
 */
class Brand extends CommonModel{

    private $errors =array(); /*错误信息*/

    /**
     * 添加一个商品品牌,$audit为true时表示审核新增
     * @param $arr & 品牌数据数组
     * @param bool $audit 审核标识位
     * @return bool
     */
    public function addGoodsBrand($arr , $audit = false)
    {
        /*初始化*/
        $goods_brand = new GoodsBrand();
        $my_file     = new MyFile();

        if(!array_has($arr, 'merchant_id'))
        {
            $this->errors['code'] = 1;
            $this->errors['messages'] = '未找到必要的参数 merchant_id';
            return false;
        }
        /*添加品牌*/
        $goods_brand->category_id = $arr['category_id'];
        $goods_brand->merchant_id = $arr['merchant_id'];
        $goods_brand->brand_logo  = request()->hasFile('brand_logo') ? $my_file->uploadBrand(request('brand_logo')) : $arr['brand_logo'];
        $goods_brand->brand_name  = $arr['brand_name'];
        $goods_brand->brand_description = $arr['brand_description'];
        $goods_brand->brand_sort = $arr['brand_sort'];
        $goods_brand->save();

        if($audit)
        {
            Rbac::adminLog('审核通过商品品牌:'.$goods_brand->brand_name."($goods_brand->brand_id)");
        }
        else
        {
            Rbac::adminLog('新增商品品牌:'.$goods_brand->brand_name."($goods_brand->brand_id)");
        }
        return true;
    }

    /**
     * 编辑一个商品品牌
     * @param $arr
     * @return bool
     */
    public function editGoodsBrand($arr)
    {
        /*初始化*/
        $goods_brand = new GoodsBrand();
        $my_file     = new MyFile();
        $edit_brand = $goods_brand->findOrFail($arr['brand_id']);

        /*编辑品牌*/
        $edit_brand->category_id = $arr['category_id'];/*分类id*/
        $edit_brand->brand_name  = $arr['brand_name'];
        $edit_brand->brand_description = $arr['brand_description'];
        $edit_brand->brand_sort  = $arr['brand_sort'];

        /*品牌Logo图片*/
        if(request()->hasFile('brand_logo'))
        {
            $edit_brand->brand_logo = $my_file->uploadBrand(request('brand_logo'));/*上传成功*/
        }
        $edit_brand->save();
        Rbac::adminLog('编辑商品品牌:'.$edit_brand->brand_name."($edit_brand->brand_id)");
        return true;
    }

    /**
     * 删除一个商品品牌
     * @param $brand_id
     * @return bool
     */
    public function deleteGoodsBrand($brand_id)
    {
        /*初始化*/
        $goods_brand  = GoodsBrand::findOrFail($brand_id);

        $goods_brand->delete();
        Rbac::adminLog('删除商品品牌:'.$goods_brand->brand_name."($goods_brand->brand_id)");
        return true;
    }

    /**
     * 获取商品品牌关联分类,商家(如有where 则加入新的sql条件)"分页,语言"
     * @param null $where = [['audit_status',$article::AWAIT_AUDIT],['category_id',151],]
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|null
     */
    public function getGoodsBrandList($where = null)
    {
        /*初始化*/
        $admin        = new Admin();
        $goods_brand  = new GoodsBrand();
        $brand_list   = null;

        /*预加载ORM对象*/
        if(!empty($where) && is_array($where))
        {
            $brand_list = $goods_brand->with('ho_goods_category','ho_merchant_info')->orderBy('brand_sort','asc')->where($where)->paginate($_COOKIE['AdminPaginationSize']);
        }
        else
        {
            $brand_list = $goods_brand->with('ho_goods_category','ho_merchant_info')->orderBy('brand_sort','asc')->paginate($_COOKIE['AdminPaginationSize']);
        }

        /*数据过滤排版*/
        $brand_list->transform(function($item) use($admin)
        {
            $item->goods_category = $item->ho_goods_category;
            $item->merchant_info = $item->ho_merchant_info;
            $item->brand_logo = MyFile::makeUrl($item->brand_logo);
            $item->goods_category->name = CommonModel::languageFormat($item->goods_category->category_name ,$item->goods_category->category_en_name );
            /*添加自营店铺信息*/
            if(empty($item->merchant_info))
            {
                $item->merchant_info = $admin->getAdminMerchantInfo();
            }
            return $item;

        });
        return $brand_list;
    }

    /**
     * 获取单个商品品牌与对应分类的关联数据
     * @param $brand_id
     * @return mixed
     */
    public function getOneGoodsBrandRelationCategory($brand_id)
    {
        /*初始化*/
        $goods_brand  = new GoodsBrand();
        /*查询*/
        $data = $goods_brand->findOrFail($brand_id);

        /*数据过滤*/
        $data->goods_category = $data->ho_goods_category;
        $data->brand_logo = MyFile::makeUrl($data->brand_logo);

        return $data;
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