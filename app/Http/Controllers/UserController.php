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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * 用户控制器
 * Class IndexController
 * @package App\Http\Controllers\Admin
 */
class UserController extends Controller
{
    public $ViewData = array(); /*传递页面的数组*/

    /**
     * View 全部用户日志列表 页面 (搜索条件参数: 身份标识, 用户姓名)
     * @param int $identity
     * @param string $nick_name
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function LogList($identity = 0, $nick_name = 'null')
    {
        /*初始化*/
        $user = new User();
        $this->ViewData['log_list'] = array();
        $where = array();

        /*条件搜索*/
        switch ($identity)
        {
            case User::ARMY_ADMIN :
                array_push($where, ['users.identity', '=', User::ARMY_ADMIN]);
                break;
            case User::PLATFORM_ADMIN :
                array_push($where, ['users.identity', '=', User::PLATFORM_ADMIN]);
                break;
            case User::SUPPLIER_ADMIN :
                array_push($where, ['users.identity', '=', User::SUPPLIER_ADMIN]);
                break;
            case User::ADMINISTRATOR :
                array_push($where, ['users.identity', '=', User::ADMINISTRATOR]);
                break;
        }
        if (!empty($nick_name) && $nick_name != 'null')
        {
            array_push($where, ['users.nick_name', 'like', '%' . $nick_name . '%']);
        }

        $this->ViewData['log_list'] = $user->getLogList($where);
        dump($this->ViewData);
        return view('log_list', $this->ViewData);
    }

    /**
     * View 当前用户日志列表 页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function LogManage()
    {
        /*初始化*/
        $user = new User();
        $this->ViewData['log_list'] = array();

        $this->ViewData['log_list'] = $user->getLogManage();
        dump($this->ViewData);
        return view('log_manage', $this->ViewData);
    }

    /**
     * View 用户管理列表 页面 (搜索条件参数: 身份标识, 禁用状态, 用户姓名, 手机号码)
     * @param int $identity
     * @param int $is_disable
     * @param string $nick_name
     * @param string $phone
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function UserList($identity = 0, $is_disable = 2, $nick_name = 'null', $phone = 'null')
    {
        /*初始化*/
        $user = new User();
        $this->ViewData['user_list'] = array();
        $where = array();

        /*条件搜索*/
        switch ($identity)
        {
            case User::ARMY_ADMIN :
                array_push($where, ['users.identity', '=', User::ARMY_ADMIN]);
                break;
            case User::PLATFORM_ADMIN :
                array_push($where, ['users.identity', '=', User::PLATFORM_ADMIN]);
                break;
            case User::SUPPLIER_ADMIN :
                array_push($where, ['users.identity', '=', User::SUPPLIER_ADMIN]);
                break;
            case User::ADMINISTRATOR :
                array_push($where, ['users.identity', '=', User::ADMINISTRATOR]);
                break;
        }
        switch ($is_disable)
        {
            case User::NO_DISABLE :
                array_push($where, ['users.is_disable', '=', User::NO_DISABLE]);
                break;
            case User::IS_DISABLE :
                array_push($where, ['users.is_disable', '=', User::IS_DISABLE]);
                break;
        }
        if (!empty($nick_name) && $nick_name != 'null')
        {
            array_push($where, ['users.nick_name', 'like', '%' . $nick_name . '%']);
        }
        if (!empty($phone) && $phone != 'null')
        {
            array_push($where, ['users.phone', 'like', '%' . $phone . '%']);
        }

        $this->ViewData['page_search'] = array('identity' => $identity, 'is_disable' => $is_disable, 'nick_name' => $nick_name, 'phone' => $phone);
        $this->ViewData['user_list'] = $user->getUserList($where);
        dump($this->ViewData);
        return view('user_list', $this->ViewData);
    }

    /**
     * View 使用原密码方式修改密码 页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function PasswordOriginalView()
    {
        /*初始化*/

        dump($this->ViewData);
        return view('password_original', $this->ViewData);
    }

    /**
     * Ajax 使用原密码方式修改密码 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function PasswordOriginalEdit(Request $request)
    {
        /*初始化*/
        $manage_u = session('ManageUser');
        $user = new User();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'original_password' => 'required',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $user->editPasswordOriginal($manage_u->user_id, $request->input('original_password'), $request->input('password')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '密码修改成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['user'] = $user->messages();
            if ($m3result->data['user']['code'] == 1)
            {
                $m3result->code = 2;
                $m3result->messages = $m3result->data['user']['messages'];
            }
        }

        return $m3result->toJson();
    }

    /**
     * View 用户添加与编辑 页面
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function UserView($id = 0)
    {
        /*初始化*/
        $manage_u = session('ManageUser');
        $user = new User();
        $this->ViewData['user_info'] = array();

        if ($id > 0)
        {
            $this->ViewData['user_info'] = $user->getUser($id);
        }
        dump($this->ViewData);
        return view('user_view', $this->ViewData);
    }

    /**
     * Ajax 检测用户名是否可用 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function UserCheckName(Request $request)
    {
        /*初始化*/
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'user_name' => 'required|unique:users,user_name',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes())
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '用户名可以使用';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '用户名被占用';
            $m3result->data['validator'] = $validator->messages();
        }
        return $m3result->toJson();
    }

    /**
     * Ajax 用户启用 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function UserEnable(Request $request)
    {
        /*初始化*/
        $user = new User();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users')->where(function ($query)
                {
                    $query->where('user_id', $GLOBALS['request']->input('user_id'))->whereIn('identity', [User::ARMY_ADMIN, User::PLATFORM_ADMIN, User::SUPPLIER_ADMIN])->where('is_disable', User::IS_DISABLE);
                }),
            ]
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes() && $user->disableOrEnableUser($request->input('user_id')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '用户启用成功';
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

    /**
     * Ajax 用户禁用 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function UserDisable(Request $request)
    {
        /*初始化*/
        $user = new User();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users')->where(function ($query)
                {
                    $query->where('user_id', $GLOBALS['request']->input('user_id'))->whereIn('identity', [User::ARMY_ADMIN, User::PLATFORM_ADMIN, User::SUPPLIER_ADMIN])->where('is_disable', User::NO_DISABLE);
                }),
            ]
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes() && $user->disableOrEnableUser($request->input('user_id')))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '用户禁用成功';
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

    /**
     * Ajax 用户添加 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function UserAdd(Request $request)
    {
        /*初始化*/
        $user = new User();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'nick_name' => 'required',
            'phone' => [
                'required',
                'numeric',
                'regex:/^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8])|(19[7]))\d{8}$/',
            ],
            'identity' => [
                'required',
                'integer',
                Rule::in([User::ARMY_ADMIN, User::PLATFORM_ADMIN, User::SUPPLIER_ADMIN]),
            ],
            'user_name' => 'required|between:4,16|unique:users,user_name',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $user->addUser($request->all()))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '用户添加成功';
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

    /**
     * Ajax 用户修改 请求处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function UserEdit(Request $request)
    {
        /*初始化*/
        $user = new User();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users')->where(function ($query)
                {
                    $query->where('user_id', $GLOBALS['request']->input('user_id'))->whereIn('identity', [User::ARMY_ADMIN, User::PLATFORM_ADMIN, User::SUPPLIER_ADMIN]);
                }),
            ],
            'phone' => [
                'required',
                'numeric',
                'regex:/^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8])|(19[7]))\d{8}$/',
            ],
            'nick_name' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        /*密码增加规则*/
        $validator->sometimes('password', 'required|min:6|confirmed', function ($input)
        {
            return !empty($input->password);/*return true时才增加验证规则!*/
        });

        /*确认密码增加规则*/
        $validator->sometimes('password_confirmation', 'required|min:6', function ($input)
        {
            return !empty($input->password_confirmation);/*return true时才增加验证规则!*/
        });

        if ($validator->passes() && $user->editUser($request->all()))
        {   /*验证通过并且处理成功*/
            $m3result->code = 0;
            $m3result->messages = '用户修改成功';
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

}