<?php

namespace App\Http\Middleware;

use App\Models\CommonModel;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Route;

/**
 * admin后台登录session验证与权限检测的中间件
 * Class AdminLoginAndPrivilege
 * @package App\Http\Middleware
 */

/**
 * 网页: 用户登录session验证与权限检测的中间件
 * Class LoginAndPrivilege
 * @package App\Http\Middleware
 */
class WebLoginAndPrivilege
{
    /**
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = Route::current();/*当前路由对象*/

        /*中间件白名单数组(不需要登录验证的白名单)*/
        $filterable = array(
//            action('Admin\IndexController@Login'),/*登录页面*/
//            action('Admin\IndexController@Logout'),/*退出登录*/
//            action('Admin\IndexController@LoginSubmit'),/*登录处理*/
//            action('Admin\IndexController@NoPrivilege'),/*没有权限页面*/
//            url('admin/language/{lang}'),/*当前语言更改*/
        );

        if (in_array(url($route->uri), $filterable) || empty($route->action['identity']))
        {
            return $next($request);
        }

        /*判断是否登录  已登录状态ManageUser === true*/
        if ($request->session()->exists('ManageUser') === true)
        {
            /*权限检测*/
            if ($request->session()->get('ManageUser')->identity == User::ADMINISTRATOR)/*超级管理员*/
            {
                return $next($request);
            }
            elseif ($request->session()->get('ManageUser')->identity == User::PLATFORM_ADMIN && in_array(User::PLATFORM_ADMIN,$route->action['identity']))/*平台用户*/
            {
                return $next($request);
            }
            elseif ($request->session()->get('ManageUser')->identity == User::SUPPLIER_ADMIN && in_array(User::SUPPLIER_ADMIN,$route->action['identity']))/*供货商用户*/
            {
                return $next($request);
            }
            elseif ($request->session()->get('ManageUser')->identity == User::ARMY_ADMIN && in_array(User::ARMY_ADMIN,$route->action['identity']))/*军方用户*/
            {
                return $next($request);
            }
            else
            {
                return CommonModel::noPrivilegePrompt($request);/*根据请求方式,返回不同的"没有"权限的信息*/
            }
        }
        else
        {
            /*未登录跳转登录页面*/
            return redirect(action('IndexController@Login'));
        }
    }

}
