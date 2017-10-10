<?php

namespace App\Http\Controllers;
use App\Models\Admin;
use App\Models\Menu;
use App\Models\Rbac;
use App\Tools\M3Result;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Request;
/**
 * 首页控制器
 * Class IndexController
 * @package App\Http\Controllers\Admin
 */
class IndexController extends Controller
{
    public $ViewData = array(); /*传递页面的数组*/

    /**
     * 后台主框架
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function Index()
    {
        /*初始化*/
//        $menu = new Menu();
//        $admin_u = session('AdminUser');

        /*根据角色权限生成栏目菜单*/
//        if($admin_u->admin_role->is_super_management_group == Rbac::IS_SUPER_MANAGEMENT_GROUP)
//        {
//            $this->ViewData['menu_list'] = $menu->getAdminMenus();
//        }
//        else
//        {
//            $this->ViewData['menu_list'] = $menu->getAdminFilterMenus();
//
//        }
        return view('index',$this->ViewData);
    }

    /**
     * 后台首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function Welcome()
    {
        return view('welcome',$this->ViewData);
    }

    /**
     * 登录页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function Login()
    {
        return view('login',$this->ViewData);
    }




/********************************************************************************************************************/

    /**
     * 用户登出(退出)处理,跳转注册页面
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function Logout()
    {
        /*删除管理员的session*/
        session()->forget('AdminUser');
        return redirect(action('Admin\MenuController@MenusAccess'));
    }




    /**
     * 用户登录Ajax提交
     * @param Request $request
     * @return \App\Tools\json
     */
    public function LoginSubmit(Request $request)
    {
        $admin    = new Admin();
        $m3result = new M3Result();
        $user     = null;

        $rules = [
            'admin_name'     => [
                'required',
                'between:4,16',
                Rule::exists('admin_user')->where(function ($query) {
                    $query->where('admin_name',$GLOBALS['request']->input('admin_name'));
                }),
            ],
            'password'   => 'required|min:6',
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->passes() && $user = $admin->userLoginCheck($request))
        {   /*验证通过并且用户检测成功*/

            $admin->userLoginSuccess($user);/*用户登录成功的处理*/

            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('common.passwordError');
            $m3result->data['validator']  = $validator->messages();
            $m3result->data['admin']      = $admin->messages();
            if(!empty($m3result->data['admin']['userLoginCheck']['code']) && $m3result->data['admin']['userLoginCheck']['code'] == 102)
            {   /*用户已停用*/
                $m3result->code    = 2;
                $m3result->messages= $m3result->data['admin']['userLoginCheck']['messages'];
            }
        }

        return $m3result->toJson();

    }


    /**
     * 设置后台显示的语言（session）
     * @param $lang
     * @return \Illuminate\Http\RedirectResponse
     */
    public function SetLanguage(Request $request , $lang)
    {
        $request->session()->put('AdminLanguage',$lang);

        return back();
    }

    public function NoPrivilege()
    {
        return view('admin.temp.no_privilege');
    }
}