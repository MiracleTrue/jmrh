<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */
namespace App\Http\Controllers;

use App\Models\User;
use App\Tools\M3Result;
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
        $manage_u = session('ManageUser');
        dump($manage_u);

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
        return view('index', $this->ViewData);
    }

    /**
     * 后台首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function Welcome()
    {
        return view('welcome', $this->ViewData);
    }

    /**
     * 登录页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function Login()
    {
        return view('login', $this->ViewData);
    }

    /**
     * 用户登出(退出)处理,跳转注册页面
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function Logout()
    {
        /*删除管理员的session*/
        session()->forget('ManageUser');
        return redirect(action('IndexController@Login'));
    }

    /**
     * Ajax 用户登录 提交处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function LoginSubmit(Request $request)
    {
        /*初始化*/
        $user = new User();
        $m3result = new M3Result();
        $manage_u = null;

        $rules = [
            'user_name' => [
                'required',
                'between:4,16',
                Rule::exists('users')->where(function ($query)
                {
                    $query->where('user_name', $GLOBALS['request']->input('user_name'));
                }),
            ],
            'password' => 'required|min:6',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes() && $manage_u = $user->userLoginFromName($request->input('user_name'), $request->input('password')))
        {   /*验证通过并且用户检测成功*/
            $user->userLoginSuccess($manage_u);/*用户登录成功的处理*/
            $m3result->code = 0;
            $m3result->messages = '用户登录成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['user'] = $user->messages();
        }
        return $m3result->toJson();
    }




    /********************************************************************************************************************/


    /**
     * 设置后台显示的语言（session）
     * @param $lang
     * @return \Illuminate\Http\RedirectResponse
     */
    public function SetLanguage(Request $request, $lang)
    {
        $request->session()->put('AdminLanguage', $lang);

        return back();
    }

    public function NoPrivilege()
    {
        return view('admin.temp.no_privilege');
    }
}