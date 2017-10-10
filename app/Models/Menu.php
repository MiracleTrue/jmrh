<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/18 0018
 * Time  : 14:17
 */

namespace App\Models;

use App\Entity\SystemMenus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

/**
 * Class Menu 菜单相关的模型
 * @package App\Models
 */
class Menu extends CommonModel{

    private $errors; /*错误信息*/

    /**
     * 后台菜单的面包屑导航,根据访问url自动识别,中英文自动转换..(不包括第一等级的"首页"!)
     * @return array  Array ( [0] => Array ( [name] => 系统管理 [url] => javascript:void(0); ) [1] => Array ( [name] => 栏目管理 [url] => http://127.0.0.5/admin/setting/menus/index ) )
     */
    public static function getAdminPosition()
    {
        /*初始化*/
        $arr = array();
        $controller = null;
        $system_menus = new SystemMenus();
        $common_model = new CommonModel();
        $path = Request::path();//当前路径

        if(!empty($path))
        {
            $controller = $system_menus->where('menu_url',$path)->get()->first();
        }

        while(!empty($controller))
        {
            $temp['name'] = $common_model->languageFormat($controller->menu_name , $controller->menu_en_name);
            if(!empty($controller->menu_url))
            {
                $temp['url']  = url($controller->menu_url);
            }
            else
            {
                $temp['url']  = 'javascript:void(0);';
            }
            array_unshift($arr,$temp);

            if($controller && $controller->parent_id > 0)
            {
                $controller = $system_menus->where('menu_id',$controller->parent_id)->get()->first();
            }
            else
            {
                $controller = null;
            }
        }
        return $arr;
    }
    
    /**
     * 返回 模型 发生的错误信息
     * @return mixed
     */
    public function messages()
    {
        return $this->errors;
    }

    /** 获取所有 系统后台 栏目 后台首页分级格式 （最多二级分组）
     * @param string $order_by 排序
     * @return mixed
     */
    public function getAdminMenus($order_by = 'asc')
    {
        /*初始化*/
        $system_menus = new SystemMenus();
        $arr = array();

        $menus_parent = $system_menus->where('parent_id',0)->orderBy('menu_sort',$order_by)->get();
        $menus_parent->transform(function($item) use($system_menus,$order_by)
        {
            $item['child'] = $system_menus->where('parent_id',$item['menu_id'])->orderBy('menu_sort',$order_by)->get();
            return $item;
        });

        return $menus_parent;
    }


    /**
     * 获取普通角色 系统后台 栏目 后台首页分级格式 （最多二级分组）
     * @return mixed
     */
    public function getAdminFilterMenus()
    {
        /*初始化*/
        $admin_u = session('AdminUser');
        $all_menu = $this->getAdminMenus();

        $role_action_arr = explode(',',$admin_u->admin_role->role_action);/*转换角色访问路由的数组*/
        $all_menu->transform(function($item) use($role_action_arr)
        {
            /*判断菜单列表如不在角色的访问路由中则删除*/
            foreach($item['child'] as $key => $value)
            {

                if(!in_array($value->menu_url ,$role_action_arr ))
                {
                    unset($item['child'][$key]);
                }
            }

            /*判断子栏目是否是空的,如果为空删除父级栏目*/
            if(!$item['child']->isEmpty())
            {
                return $item;
            }
            else
            {
                return null;
            }
        });

        /*过滤空集合并返回*/
        return $all_menu->filter();
    }

    /**
     * 增加一条 系统后台 栏目记录
     * @param $request
     * @return bool
     */
    public function addAdminMenu($request)
    {
        /*初始化*/
        $system_menus = new SystemMenus();

        $system_menus->parent_id = $request->input('parent_id');
        $system_menus->menu_name = $request->input('menu_name');
        $system_menus->menu_en_name = $request->input('menu_en_name');
        $system_menus->menu_sort = $request->input('menu_sort');
        $system_menus->menu_icon = $request->has('menu_icon') ? $request->input('menu_icon') : '&#xe603;';/*默认图标*/
        $system_menus->menu_url  = $request->has('menu_url')  ? $request->input('menu_url') : '';
        $system_menus->menu_controller = $request->has('menu_controller') ? $request->input('menu_controller') : '';
        $system_menus->save();
        Rbac::adminLog('新增栏目:'.$system_menus->menu_name."($system_menus->menu_id)");

        return true;
    }

    /**
     * 更新一条 系统后台 栏目记录
     * @param $request
     * @return bool
     */
    public function editAdminMenu($request)
    {
        /*初始化*/
        $system_menus = new SystemMenus();
        $edit_menu = $system_menus->find($request->input('menu_id'));

        if($edit_menu)
        {
            $edit_menu->parent_id = $request->input('parent_id');
            $edit_menu->menu_name = $request->input('menu_name');
            $edit_menu->menu_en_name = $request->input('menu_en_name');
            $edit_menu->menu_sort = $request->input('menu_sort');
            $edit_menu->menu_icon = $request->has('menu_icon') ? $request->input('menu_icon') : '&#xe603;';/*默认图标*/
            $edit_menu->menu_url  = $request->has('menu_url')  ? $request->input('menu_url') : '';
            $edit_menu->menu_controller = $request->has('menu_controller') ? $request->input('menu_controller') : '';
            $edit_menu->save();
            Rbac::adminLog('修改栏目:'.$edit_menu->menu_name."($edit_menu->menu_id)");

            return true;
        }
        else
        {
            return false;
        }
    }


    /**
     * 删除 系统后台 栏目记录 如果是父栏目子栏目会一并删除
     * @param $request
     * @return bool
     */
    public function deleteAdminMenu($request)
    {
        /*初始化*/
        $system_menus = new SystemMenus();
        $child_menus  = null;

        $delete_menu = $system_menus->find($request->input('menu_id'));
        if($delete_menu->parent_id == 0)
        {
            $child_menus = $system_menus->where('parent_id',$delete_menu->menu_id)->get();
        }

        $delete_menu->delete();

        if($child_menus)
        {
            $system_menus->where('parent_id',$delete_menu->menu_id)->delete();
        }
        Rbac::adminLog('删除栏目:'.$delete_menu->menu_name."($delete_menu->menu_id)");
        return true;
    }

}