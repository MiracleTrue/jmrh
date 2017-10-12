<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/18 0018
 * Time  : 14:17
 */

namespace App\Models;
use App\Entity\GoodsInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class Goods 商品 主模型
 * @package App\Models
 */
class Goods extends CommonModel{

    private $errors =array(); /*错误信息*/

    /*商品类型*/
    const NORMAL_GOODS = 0;    /*普通商品*/
    const PRE_GOODS = 1;        /*预售商品*/
    const EXCHANGE_GOODS = 2;  /*积分兑换商品*/

    /*审核发布状态*/
    const AWAIT_SALE = 0;      /*商品等待审核*/
    const IS_SALE = 1;         /*商品已上架,正在销售*/
    const NO_SALE = 2;         /*商品下架,禁止销售*/

    /*删除状态*/
    const NO_DELETE = 0;       /*商品正常*/
    const IS_DELETE = 1;       /*商品已删除,加入回收站*/


    /**
     * 获取单个商品与对应的关联数据(用于商品编辑页面)
     * @param $goods_id
     * @return mixed
     */
    public function getOneGoodsRelationAll($goods_id)
    {
        /*初始化*/
        $goods_info = new GoodsInfo();

        /*查询*/
        $data = $goods_info->findOrFail($goods_id);

        /*数据转换*/
        $data->goods_category = $data->ho_goods_category;
        $data->goods_brand    = $data->ho_goods_brand;
        $data->goods_thumb = MyFile::makeUrl($data->goods_thumb);
        $data->goods_price = $this->formatGoodsPrice($data->goods_price);
        $data->goods_photo = explode(',',$data->goods_photo);
        $data->select_attr    = json_decode($data->select_attr,true);
        $data->show_attr      = json_decode($data->show_attr,true);
        return $data;
    }

    /**
     * 更新一个商品的上下架状态
     * @param $goods_id
     * @param null $status
     * @return bool
     */
    public function auditGoodsInfo($goods_id , $status = null)
    {
        /*初始化*/
        $goods_info  = new GoodsInfo();

        if($goods_id > 0 && in_array($status,[self::IS_SALE,self::NO_SALE]))
        {
            $edit_goods = $goods_info->findOrFail($goods_id);
            $edit_goods->sale_status = $status;
            if($status == self::NO_SALE)
            {
                $edit_goods->sale_time = 0;
                Rbac::adminLog('商品下架:'.$edit_goods->goods_name."($edit_goods->goods_id)");
            }
            elseif($status == self::IS_SALE)
            {
                $edit_goods->sale_time = Carbon::now()->timestamp;
                Rbac::adminLog('商品上架:'.$edit_goods->goods_name."($edit_goods->goods_id)");

            }
            $edit_goods->save();
            return true;
        }
        else
        {
            $this->errors['code'] = 1;
            $this->errors['messages'] = '非法参数';
            return false;
        }
    }

    /**
     * 删除一个商品(移入回收站)
     * @param $goods_id
     * @return bool
     */
    public function deleteGoodsInfo($goods_id)
    {
        /*初始化*/
        $goods_info   = GoodsInfo::findOrFail($goods_id);
        $goods_info->is_delete = self::IS_DELETE;
        $goods_info->sale_status = self::AWAIT_SALE;
        $goods_info->sale_time = 0;
        $goods_info->save();

        Rbac::adminLog('删除商品(移入回收站):'.$goods_info->goods_name."($goods_info->goods_id)");
        return true;
    }

    /**
     * 永久销毁一个商品
     * @param $goods_id
     * @return bool
     */
    public function destroyGoodsInfo($goods_id)
    {
        /*初始化*/
        $goods_info   = GoodsInfo::findOrFail($goods_id);
        $goods_info->delete();
        Rbac::adminLog('销毁商品:'.$goods_info->goods_name."($goods_info->goods_id)");
        return true;
    }

    /**
     * 恢复一个商品
     * @param $goods_id
     * @return bool
     */
    public function recoveryGoodsInfo($goods_id)
    {
        /*初始化*/
        $goods_info   = GoodsInfo::findOrFail($goods_id);
        $goods_info->is_delete = self::NO_DELETE;
        $goods_info->sale_status = self::AWAIT_SALE;
        $goods_info->sale_time = 0;
        $goods_info->save();

        Rbac::adminLog('恢复商品:'.$goods_info->goods_name."($goods_info->goods_id)");
        return true;
    }

    /**
     * 获取商品关联"分类" "品牌" "商家" 的列表(如有where 则加入新的sql条件)"分页"
     * @param null $where = [['audit_status',$article::AWAIT_AUDIT],['category_id',151],]
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|null
     */
    public function getGoodsListAll($where = null)
    {
        /*初始化*/
        $goods_info = new GoodsInfo();
        $admin      = new Admin();
        $data = null;

        /*预加载ORM对象*/
        if(!empty($where) && is_array($where))
        {
            $data = $goods_info->with('ho_goods_category','ho_goods_brand','ho_merchant_info')->orderBy('goods_id','asc')->where($where)->paginate($_COOKIE['AdminPaginationSize']);
        }
        else
        {
            $data = $goods_info->with('ho_goods_category','ho_goods_brand','ho_merchant_info')->orderBy('goods_id','asc')->paginate($_COOKIE['AdminPaginationSize']);
        }

        /*数据过滤排版*/
        $data->transform(function($item) use ($admin)
        {
            $item->goods_category = $item->ho_goods_category;
            $item->goods_category->name = CommonModel::languageFormat($item->goods_category->category_name ,$item->goods_category->category_en_name );
            $item->goods_brand = $item->ho_goods_brand;
            $item->goods_brand->brand_logo = MyFile::makeUrl($item->goods_brand->brand_logo);
            $item->goods_price = $this->formatGoodsPrice($item->goods_price);
            $item->goods_thumb = MyFile::makeUrl($item->goods_thumb);
            $item->add_time = Carbon::createFromTimestamp($item->add_time);
            $item->sale_time = $item->sale_time ? Carbon::createFromTimestamp($item->sale_time) : '';
            $item->merchant_info = $item->ho_merchant_info;
            /*添加自营店铺信息*/
            if(empty($item->merchant_info))
            {
                $item->merchant_info = $admin->getAdminMerchantInfo();
            }

            return $item;
        });

        return $data;
    }
    
    /**
     * 增加一个 普通商品
     * @param $arr
     * @return bool
     */
    public function addNormalGoods($arr)
    {
        /*初始化*/
        $goods_info  = new GoodsInfo();
        $my_file     = new MyFile();

        /*验证必要数据*/
        if(!array_has($arr, 'merchant_id'))
        {
            $this->errors['code'] = 1;
            $this->errors['messages'] = '未找到必要的参数 merchant_id';
            return false;
        }

        if(!empty($arr['show_attr'])  &&  !$arr['show_attr'] = $this->checkShowJson($arr['show_attr']))
        {
            $this->errors['code'] = 2;
            $this->errors['messages'] = 'show_attr 验证失败';
            return false;
        }

        if(!empty($arr['select_attr'])  &&  !$arr['select_attr'] = $this->checkSelectJson($arr['select_attr'] , $arr['goods_price'] , $arr['goods_number']))
        {
            $this->errors['code'] = 3;
            $this->errors['messages'] = 'select_attr 验证失败';
            return false;
        }

        /*相册过滤与转换成字符串*/
        $arr['goods_photo'] = array_where($arr['goods_photo'], function ($value) {
            return !empty($value);
        });
        $arr['goods_photo'] = implode(',',$arr['goods_photo']);

        /*添加商品*/
        $goods_info->merchant_id = $arr['merchant_id'];
        $goods_info->category_id = $arr['category_id'];
        $goods_info->brand_id = $arr['brand_id'];
        $goods_info->goods_type = self::NORMAL_GOODS;
        $goods_info->goods_name = $arr['goods_name'];
        $goods_info->goods_number = $arr['goods_number'];
        $goods_info->goods_sort = $arr['goods_sort'];
        $goods_info->goods_price = $this->formatGoodsPrice($arr['goods_price']);
        $goods_info->goods_thumb  = request()->hasFile('goods_thumb') ? $my_file->uploadThumb(request('goods_thumb')) : $arr['goods_thumb'];
        $goods_info->goods_description = !empty($arr['goods_description']) ? $arr['goods_description'] : '';
        $goods_info->goods_photo = $arr['goods_photo'];
        $goods_info->goods_content = !empty($arr['goods_content']) ? $arr['goods_content'] : '';
        $goods_info->show_attr = !empty($arr['show_attr']) ? $arr['show_attr'] : '';
        $goods_info->select_attr = !empty($arr['select_attr']) ? $arr['select_attr'] : '';
        $goods_info->sale_status = self::AWAIT_SALE;
        $goods_info->buy_count = 0;
        $goods_info->add_time = Carbon::now()->timestamp;
        $goods_info->sale_time = 0;

        $goods_info->save();
        Rbac::adminLog('新增商品:'.$goods_info->goods_name."($goods_info->goods_id)");
        return true;
    }

    /**
     * 编辑一个 普通商品
     * @param $arr
     * @return bool
     */
    public function editNormalGoods($arr)
    {
        /*初始化*/
        $goods_info  = GoodsInfo::findOrFail($arr['goods_id']);
        $my_file     = new MyFile();

        /*验证必要数据*/
        if(!empty($arr['show_attr'])  &&  !$arr['show_attr'] = $this->checkShowJson($arr['show_attr']))
        {
            $this->errors['code'] = 2;
            $this->errors['messages'] = 'show_attr 验证失败';
            return false;
        }

        if(!empty($arr['select_attr'])  &&  !$arr['select_attr'] = $this->checkSelectJson($arr['select_attr'] , $arr['goods_price'] , $arr['goods_number']))
        {
            $this->errors['code'] = 3;
            $this->errors['messages'] = 'select_attr 验证失败';
            return false;
        }

        /*相册过滤与转换成字符串*/
        $arr['goods_photo'] = array_where($arr['goods_photo'], function ($value) {
            return !empty($value);
        });
        $arr['goods_photo'] = implode(',',$arr['goods_photo']);

        /*添加商品*/
        $goods_info->category_id = $arr['category_id'];
        $goods_info->brand_id = $arr['brand_id'];
        $goods_info->goods_type = self::NORMAL_GOODS;
        $goods_info->goods_name = $arr['goods_name'];
        $goods_info->goods_number = $arr['goods_number'];
        $goods_info->goods_sort = $arr['goods_sort'];
        $goods_info->goods_price = $this->formatGoodsPrice($arr['goods_price']);
        if(request()->hasFile('goods_thumb'))
        {
            $goods_info->goods_thumb  = $my_file->uploadThumb(request('goods_thumb'));
        }
        $goods_info->goods_description = !empty($arr['goods_description']) ? $arr['goods_description'] : '';
        $goods_info->goods_photo = $arr['goods_photo'];
        $goods_info->goods_content = !empty($arr['goods_content']) ? $arr['goods_content'] : '';
        $goods_info->show_attr = !empty($arr['show_attr']) ? $arr['show_attr'] : '';
        $goods_info->select_attr = !empty($arr['select_attr']) ? $arr['select_attr'] : '';

        $goods_info->save();
        Rbac::adminLog('编辑商品:'.$goods_info->goods_name."($goods_info->goods_id)");
        return true;
    }

    /**
     * 验证 商品展示属性 json数据 (验证成功返回可以入库的json)
     * @param $json & show_attr
     * @return bool|string
     */
    public function checkShowJson($json)
    {
        $arr = json_decode($json,true);

        $arr = collect($arr)->unique('name')->all();/*去重*/

        $rules = [
            "*"      => 'required|array',
            "*.name" => 'required',
            "*.data" => 'required',
        ];
        $validator = Validator::make($arr, $rules);
        if($validator->fails())
        {
            return false;
        }
        else
        {   /*验证成功*/
            return json_encode($arr);
        }
    }


    /**
     * 验证 商品购买属性 json数据 (验证成功返回可以入库的json)
     * @param $json & select_attr
     * @param $price & 商品价格
     * @param $number & 商品总数量
     * @return bool|string
     */
    public function checkSelectJson($json,$price,$number)
    {
        $arr = json_decode($json,true);
        $arr = collect($arr)->unique('name')->all();/*去重*/

        /*必填项验证与价格验证*/
        $rules = [
            "*"      => 'required|array',
            "*.type" => [
                'required',
                Rule::in([Attributes::SELECT_TYPE_TEXT,Attributes::SELECT_TYPE_PHOTO]),
            ],
            "*.data" => 'required',
            "*.data.*.text" => 'required',
            "*.data.*.price" => 'required|numeric|min:'.$price,
            "*.data.*.number" => 'required|integer',
            "*.data.*.extra" => 'required_if:*.type,'.Attributes::SELECT_TYPE_PHOTO,
        ];
        $validator = Validator::make($arr, $rules);

        /*商品库存数量验证*/
        $count_number = collect($arr)->sum(function ($product) {
            return collect($product['data'])->sum('number');
        });

        if($validator->fails() || $number != $count_number)
        {
            return false;
        }
        else
        {   /*验证成功*/
            return json_encode($arr);
        }

    }



    /**
     * 统一商品价格,舍去法取小数后两位即:120.68(强制保留2位小数)
     * @param $price
     * @return float
     */
    public function formatGoodsPrice($price)
    {
        if($price > 0)
        {
            return sprintf("%.2f",substr(sprintf("%.3f", $price), 0, -1));
        }
        else
        {
            return '0.00';
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