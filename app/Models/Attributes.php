<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/18 0018
 * Time  : 14:17
 */

namespace App\Models;
use App\Entity\GoodsAttributes;

/**
 * Class Attributes 商品属性的模型
 * @package App\Models
 */
class Attributes extends CommonModel{

    const PRESET = 'preset';  /*属性type  预设选择  商品录入时手工输入*/
    const DEFINED= 'defined'; /*属性type  自定义    商品录入时在列表选择*/

    const SELECT_TYPE_TEXT = 'text';    /*购买属性type 普通文本*/
    const SELECT_TYPE_PHOTO = 'photo';  /*购买属性type 图片属性*/
    private $errors =array(); /*错误信息*/

    /**
     * 获取商品属性关联分类的列表(如有where 则加入新的sql条件)"分页"
     * @param null $where = [['audit_status',$article::AWAIT_AUDIT],['category_id',151],]
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|null
     */
    public function getGoodsAttributesAll($where = null)
    {
        /*初始化*/
        $goods_attributes = new GoodsAttributes();
        $data = null;

        /*预加载ORM对象*/
        if(!empty($where) && is_array($where))
        {
            $data = $goods_attributes->with('ho_goods_category')->orderBy('attr_id','asc')->where($where)->paginate($_COOKIE['AdminPaginationSize']);
        }
        else
        {
            $data = $goods_attributes->with('ho_goods_category')->orderBy('attr_id','asc')->paginate($_COOKIE['AdminPaginationSize']);
        }

        /*数据过滤排版*/
        $data->transform(function($item)
        {
            $item->goods_category = $item->ho_goods_category;
            $item->goods_category->name = CommonModel::languageFormat($item->goods_category->category_name ,$item->goods_category->category_en_name );
            $select_arr = json_decode($item->select_attr,true);
            $show_arr = json_decode($item->show_attr,true);
            $item->attr_count   = count($select_arr) + count($show_arr);
            $item->select_attr  = '';
            $item->show_attr    = '';

            if(is_array($select_arr))
            {
                foreach($select_arr as $key => $value)
                {
                    if($select_arr[$key] == end($select_arr)) {
                        $item->select_attr .= $select_arr[$key]['name'];
                    } else {
                        $item->select_attr .= $select_arr[$key]['name'] . ' | ';
                    }
                }
            }
            if(is_array($show_arr))
            {
                foreach($show_arr as $key => $value)
                {
                    if($show_arr[$key] == end($show_arr)) {
                        $item->show_attr .= $show_arr[$key]['name'];
                    } else {
                        $item->show_attr .= $show_arr[$key]['name'] . ' | ';
                    }
                }
            }
            return $item;
        });
        
        return $data;
    }

    /**
     * 获取单个商品属性与对应分类的关联数据(转换Json->数组格式)
     * @param $attr_id
     * @return mixed
     */
    public function getOneGoodsAttributesRelationCategory($attr_id)
    {
        /*初始化*/
        $goods_attributes = new GoodsAttributes();
        /*查询*/
        $data = $goods_attributes->findOrFail($attr_id);
        /*数据过滤*/
        $data->goods_category = $data->ho_goods_category;
        $data->select_attr    = json_decode($data->select_attr,true);
        $data->show_attr      = json_decode($data->show_attr,true);
        return $data;
    }

    /**
     * 添加一个商品属性
     * @param $arr
     * @return bool
     */
    public function addGoodsAttributes($arr)
    {
        /*初始化*/
        $goods_attributes = new GoodsAttributes();
        $arr['show_attr'] = (!empty($arr['show_attr'])) ? $arr['show_attr'] : '';
        $arr['select_attr'] = (!empty($arr['select_attr'])) ? $arr['select_attr'] : '';

        $goods_attributes->category_id = $arr['category_id'];
        $goods_attributes->attr_name = $arr['attr_name'];
        $goods_attributes->select_attr = $arr['select_attr'];
        $goods_attributes->show_attr = $arr['show_attr'];
        $goods_attributes->save();
        Rbac::adminLog('新增商品属性:'.$goods_attributes->attr_name."($goods_attributes->attr_id)");
        return true;
    }

    /**
     * 更新一个商品属性
     * @param $arr
     * @return bool
     */
    public function editGoodsAttributes($arr)
    {
        /*初始化*/
        $goods_attributes = GoodsAttributes::findOrFail($arr['attr_id']);
        $arr['show_attr'] = (!empty($arr['show_attr'])) ? $arr['show_attr'] : '';
        $arr['select_attr'] = (!empty($arr['select_attr'])) ? $arr['select_attr'] : '';

        $goods_attributes->category_id = $arr['category_id'];
        $goods_attributes->attr_name = $arr['attr_name'];
        $goods_attributes->select_attr = $arr['select_attr'];
        $goods_attributes->show_attr = $arr['show_attr'];
        $goods_attributes->save();
        Rbac::adminLog('编辑商品属性:'.$goods_attributes->attr_name."($goods_attributes->attr_id)");
        return true;
    }

    /**
     * 删除一个商品属性
     * @param $attr_id
     * @return bool
     */
    public function deleteGoodsAttributes($attr_id)
    {
        /*初始化*/
        $goods_attributes = GoodsAttributes::findOrFail($attr_id);
        $goods_attributes->delete();
        Rbac::adminLog('删除商品属性:'.$goods_attributes->attr_name."($goods_attributes->attr_id)");
        return true;
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