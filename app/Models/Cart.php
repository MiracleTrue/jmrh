<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 13:38
 */

namespace App\Models;

use App\Entity\ShoppingCart;
use Carbon\Carbon;

/**
 * Class Product 购物车相关模型
 * @package App\Models
 */
class Cart extends CommonModel
{
    /**
     * 获取所有购物车产品列表 (如有where 则加入新的sql条件) "分页" | 默认排序:创建时间
     * @param array $where
     * @param array $orderBy
     * @param bool $is_paginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getCartList($where = array(), $orderBy = array(['shopping_cart.create_time', 'desc']), $is_paginate = true)
    {
        /*初始化*/
        $e_shopping_cart = new ShoppingCart();

        /*预加载ORM对象*/
        $e_shopping_cart = $e_shopping_cart->with('ho_users')
            ->where($where);
        foreach ($orderBy as $value)
        {
            $e_shopping_cart->orderBy($value[0], $value[1]);
        }
        /*是否需要分页数据*/
        if ($is_paginate === true)
        {
            $cart_list = $e_shopping_cart->paginate($_COOKIE['PaginationSize']);
        }
        else
        {
            $cart_list = $e_shopping_cart->get();
        }

        /*数据过滤*/
        $cart_list->transform(function ($item)
        {
            $item->user_info = $item->ho_users;
            unset($item->ho_users);
            return $item;
        });
        return $cart_list;
    }

    /**
     * 购物车加入一件产品
     * @param $user_id
     * @param $product_id
     * @param $spec_id
     * @param array $extra
     * @return bool
     */
    public function addProductToCart($user_id, $product_id, $spec_id, $extra = array())
    {
        /*初始化*/
        $product = new Product();
        try
        {
            $goods = $product->getProductInfo($product_id);
            $spec = $goods->spec_info->first(function ($value) use ($spec_id)
            {
                return $value['spec_id'] == $spec_id;
            });
            $check_data = ShoppingCart::where('product_name', $goods->product_name)->where('spec_name', $spec->spec_name)->where('user_id', $user_id)->first();

            if ($check_data != null)
            {
                $check_data->increment('product_number');
            }
            else
            {
                $e_shopping_cart = new ShoppingCart();
                /*添加*/
                $e_shopping_cart->user_id = $user_id;
                $e_shopping_cart->product_name = $goods->product_name;
                $e_shopping_cart->product_number = 1;
                $e_shopping_cart->product_thumb = MyFile::decodeUrl($goods->product_thumb);
                $e_shopping_cart->spec_name = $spec->spec_name;
                $e_shopping_cart->spec_unit = $spec->spec_unit;
                $e_shopping_cart->contact_person = !empty($extra['contact_person']) ? $extra['contact_person'] : '';
                $e_shopping_cart->contact_tel = !empty($extra['contact_tel']) ? $extra['contact_tel'] : '';
                $e_shopping_cart->note = !empty($extra['note']) ? $extra['note'] : '';
                $e_shopping_cart->army_receive_time = !empty($extra['army_receive_time']) ? strtotime($extra['army_receive_time']) : 0;/*2017-10-18 08:45:12*/
                $e_shopping_cart->create_time = Carbon::now()->timestamp;

                $e_shopping_cart->save();
            }
        } catch (\Exception $e)
        {
            return false;
        }
        return true;
    }

    /**
     * 购物车删除多件产品
     * @param $id_array
     * @param $user_id
     * @return mixed
     */
    public function deleteProductsFromCart($user_id, $id_array)
    {
        return ShoppingCart::where('user_id', $user_id)->whereIn('cart_id', $id_array)->delete();
    }

    /**
     * 改变购物车内产品数量
     * @param $user_id
     * @param $cart_id
     * @param $number
     * @return bool
     */
    public function changeProductNumberFromCart($user_id, $cart_id, $number)
    {
        /*初始化*/
        $e_shopping_cart = ShoppingCart::where('user_id', $user_id)->where('cart_id', $cart_id)->first() or die();

        /*改变数量*/
        $e_shopping_cart->product_number = $number;
        $e_shopping_cart->save();
        return true;
    }


}