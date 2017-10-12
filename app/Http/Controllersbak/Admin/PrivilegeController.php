<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/15 0015
 * Time  : 16:02
 */

namespace App\Http\Controllers\Admin;
use App\Entity\AdminRole;
use App\Entity\AdminUser;
use App\Models\Admin;
use App\Models\Menu;
use App\Models\Rbac;
use App\Tools\M3Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


/**
 * Class 后台 权限管理 控制器
 */
class PrivilegeController extends CommonController
{
    public $ViewData = array(); /*传递页面的数组*/


    /**
     * Admin系统管理员日志列表页面
     * @param int $admin_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function AdminLogIndex($admin_id = 0)
    {
        /*初始化*/
        $rbac = new Rbac();
        $this->ViewData['nav_position'] = Menu::getAdminPosition();/*面包屑*/
        if($admin_id > 0)
        {
            $this->ViewData['log_list'] = $rbac->getAdminLogList($admin_id);/*获取Admin后台系统操作的log记录(如有admin_id则获取指定用户记录)"分页,语言"*/
        }
        else
        {
            $this->ViewData['log_list'] = $rbac->getAdminLogList();/*获取Admin后台系统操作的log记录(如有admin_id则获取指定用户记录)"分页,语言"*/
        }

        return view('admin.privilege_log_index',$this->ViewData);
    }

    /**
     * Admin系统管理员日志Ajax批量删除
     * @param Request $request
     * @return \App\Tools\json
     */
    public function AdminLogBatchDelete(Request $request)
    {
        /*初始化*/
        $rbac = new Rbac();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'delete_id'   => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->passes() && $rbac->batchDeleteAdminLog($request->input('delete_id')))
        {   /*验证通过并且更新成功*/
            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failed');
            $m3result->data['validator']    = $validator->messages();
            $m3result->data['rbac']         = $rbac->messages();
        }

        return $m3result->toJson();
    }

    /**
     * Admin角色列表页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function RoleIndex()
    {
        /*初始化*/
        $rbac = new Rbac();

        $this->ViewData['count']= AdminRole::count();
        $this->ViewData['nav_position'] = Menu::getAdminPosition();/*面包屑*/
        $this->ViewData['admin_role_list'] = $rbac->getAdminRoleRelationUser();/*获取管理员角色与对应管理员用户的关联数据*/

        /*统计对应管理员的数量*/
        $this->ViewData['admin_role_list']->transform(function($item)
        {
            $item->admin_user_count = $item->admin_user->count();
            return $item;
        });

        return view('admin.privilege_role_index',$this->ViewData);
    }

    /**
     * Admin角色编辑页面
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function RoleView($id = 0)
    {
        /*初始化*/
        $rbac = new Rbac();
        $this->ViewData['role_info']   = null;
        $this->ViewData['privilege_list'] = null;

        /*判断ID决定新增还是修改*/
        if($id > 0)
        {   /*编辑数据*/
            $this->ViewData['role_info']  = AdminRole::find($id);
            $this->ViewData['privilege_list'] = $rbac->getAdminPrivilege($this->ViewData['role_info']->role_action);/*获取Admin后台所有权限*/
        }
        else
        {   /*新增数据*/
            $this->ViewData['privilege_list'] = $rbac->getAdminPrivilege();/*获取Admin后台所有权限*/
        }

        return view('admin.privilege_role_edit',$this->ViewData);
    }

    /**
     * Admin角色Ajax新增与编辑 提交处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function RoleEditSubmit(Request $request)
    {
        /*初始化*/
        $rbac = new Rbac();
        $admin_u = session('AdminUser');
        $m3result = new M3Result();

        if($request->input('role_id') == 0)/*新增角色*/
        {
            /*验证规则*/
            $rules = [
                'role_name'   => 'required',
                'is_super_management_group'     => 'required|integer',
            ];
            $validator = Validator::make($request->all(), $rules);

            if($validator->passes() && $rbac->addAdminRole($request))
            {   /*验证通过并且添加成功*/
                $m3result->code    = 0;
                $m3result->messages= __('admin.success');
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
                $m3result->data['validator']    = $validator->messages();
                $m3result->data['rbac']         = $rbac->messages();
            }
        }
        else if($request->input('role_id') > 0)/*编辑角色*/
        {
            /*验证规则*/
            $rules = [
                'role_id'     => [
                    'required',
                    'integer',
                    Rule::exists('admin_role')->where(function ($query) {
                        $query->where('role_id',$GLOBALS['request']->input('role_id'));
                    }),
                ],
                'role_name'   => 'required',
                'is_super_management_group'     => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $rules);

            if($validator->passes() && $rbac->editAdminRole($request))
            {   /*验证通过并且更新成功*/
                $m3result->code    = 0;
                $m3result->messages= __('admin.success');
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
                $m3result->data['validator']  = $validator->messages();
                $m3result->data['rbac']       = $rbac->messages();
            }
        }
        else
        {
            $m3result->code    = 2;
            $m3result->messages= '无效数据';
        }


        return $m3result->toJson();

    }

    /**
     * Admin管理员列表页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ManagerIndex()
    {
        /*初始化*/
        $rbac = new Rbac();
        $this->ViewData['nav_position'] = Menu::getAdminPosition();

        $this->ViewData['manager_list'] = $rbac->getAdminUserRelationRole();/*获取管理员用户与对应角色的关联数据*/
        $this->ViewData['manager_count'] = $this->ViewData['manager_list']->count();

        return view('admin.privilege_manager_index', $this->ViewData);
    }

    /**
     * Admin管理员编辑页面
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ManagerView($id = 0)
    {
        /*初始化*/
        $rbac = new Rbac();
        $this->ViewData['manager_info']   = null;
        $this->ViewData['role_list'] = $rbac->getAdminRoleRelationUser();

        /*判断ID决定新增还是修改*/
        if($id > 0)
        {   /*编辑数据*/
            $this->ViewData['manager_info']     = AdminUser::findOrFail($id);
        }
        else
        {   /*新增数据*/
            //$this->ViewData['privilege_list'] = $rbac->getAdminPrivilege();/*获取Admin后台所有权限*/
        }

        return view('admin.privilege_manager_edit',$this->ViewData);
    }

    /**
     * Admin角色Ajax 删除提交
     * @param Request $request
     * @return \App\Tools\json
     */
    public function RoleDeleteOne(Request $request)
    {
        /*初始化*/
        $m3result = new M3Result();
        $rbac = new Rbac();

        /*验证规则*/
        $rules = [
            'role_id'     => [
                'required',
                'integer',
                Rule::exists('admin_role')->where(function ($query) {
                    $query->where('role_id',$GLOBALS['request']->input('role_id'));
                }),
            ]
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->passes() && $rbac->deleteAdminRole($request->input('role_id')))
        {   /*验证通过*/
            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failed');
            $m3result->data['validator']    = $validator->messages();
            $m3result->data['rbac']         = $rbac->messages();
        }

        return $m3result->toJson();
    }

    /**
     * Admin管理员Ajax 删除提交
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ManagerDeleteOne(Request $request)
    {
        /*初始化*/
        $m3result = new M3Result();
        $rbac = new Rbac();

        /*验证规则*/
        $rules = [
            'admin_id'     => [
                'required',
                'integer',
                Rule::exists('admin_user')->where(function ($query) {
                    $query->where('admin_id',$GLOBALS['request']->input('admin_id'));
                }),
            ]
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->passes() && $rbac->deleteAdminUser($request->input('admin_id')))
        {   /*验证通过*/
            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failed');
            $m3result->data['validator']    = $validator->messages();
            $m3result->data['rbac']         = $rbac->messages();
        }

        return $m3result->toJson();
    }

    /**
     * Admin管理员Ajax新增与编辑 提交处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ManagerEditSubmit(Request $request)
    {
        /*初始化*/
        $rbac = new Rbac();
        $m3result = new M3Result();

        if($request->input('admin_id') == 0)/*新增管理员*/
        {
            /*验证规则*/
            $rules = [
                'admin_name'   => 'required|between:4,16|unique:admin_user',
                'password'     => 'required|confirmed|min:6',
                'phone'        => 'required|',
                'email'        => 'required|email',
                'is_enable'    => [
                    'required',
                    Rule::in([Rbac::MANAGER_IS_ENABLE, Rbac::MANAGER_NO_ENABLE]),
                ],
                'role_id'     => [
                    'required',
                    'integer',
                    Rule::exists('admin_role')->where(function ($query) {
                        $query->where('role_id',$GLOBALS['request']->input('role_id'));
                    }),
                ],
            ];
            $validator = Validator::make($request->all(), $rules);

            if($validator->passes() && $rbac->addAdminUser($request))
            {   /*验证通过并且添加成功*/
                $m3result->code    = 0;
                $m3result->messages= __('admin.success');
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
                $m3result->data['validator']    = $validator->messages();
                $m3result->data['rbac']         = $rbac->messages();
            }
        }
        else if($request->input('role_id') > 0)/*编辑管理员*/
        {
            $rules = [
                'admin_id'     => [
                    'required',
                    'integer',
                    Rule::exists('admin_user')->where(function ($query) {
                        $query->where('admin_id',$GLOBALS['request']->input('admin_id'));
                    }),
                ],
                'admin_name'   => [
                    'required',
                    'between:4,16',
                    Rule::unique('admin_user')->ignore($request->input('admin_id') , 'admin_id'),
                ],
                'password'     => 'required|confirmed|min:6',
                'phone'        => 'required|',
                'email'        => 'required|email',
                'is_enable'    => [
                    'required',
                    Rule::in([Rbac::MANAGER_IS_ENABLE, Rbac::MANAGER_NO_ENABLE]),
                ],
                'role_id'     => [
                    'required',
                    'integer',
                    Rule::exists('admin_role')->where(function ($query) {
                        $query->where('role_id',$GLOBALS['request']->input('role_id'));
                    }),
                ],
            ];

            $validator = Validator::make($request->all(), $rules);

            if($validator->passes() && $rbac->editAdminUser($request))
            {   /*验证通过并且更新成功*/
                $m3result->code    = 0;
                $m3result->messages= __('admin.success');
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
                $m3result->data['validator']    = $validator->messages();
                $m3result->data['rbac']         = $rbac->messages();
            }
        }
        else
        {
            $m3result->code    = 2;
            $m3result->messages= '无效数据';
        }


        return $m3result->toJson();
    }

    /**
     * Admin管理员 Ajax快捷编辑的异步提交与处理(在POST请求中传入与数据库对应的键值)
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ManagerQuickEdit(Request $request)
    {
        /*初始化*/
        $admin_user = new AdminUser();
        $m3result = new M3Result();
        $rbac       = new Rbac();
        $admin_u   = session('AdminUser');

        /*验证*/
        $rules = [
            'admin_id'     => [
                'required',
                'integer',
                Rule::exists('admin_user')->where(function ($query) {
                    $query->where('admin_id',$GLOBALS['request']->input('admin_id'));
                }),
            ],
            'admin_name'   => [
                'sometimes',
                'required',
                'between:4,16',
                Rule::unique('admin_user')->ignore($request->input('admin_id') , 'admin_id'),
            ],
            'phone'        => 'sometimes|required|',
            'email'        => 'sometimes|required|email',
            'is_enable'    => [
                'sometimes',
                'required',
                Rule::in([Rbac::MANAGER_IS_ENABLE, Rbac::MANAGER_NO_ENABLE]),
            ],
            'role_id'     => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('admin_role')->where(function ($query) {
                    $query->where('role_id',$GLOBALS['request']->input('role_id'));
                }),
            ],
        ];
        $validator = Validator::make($request->all(), $rules);

        if($validator->passes())
        {   /*验证通过并且更新成功*/

            /*普通管理员无法对超级管理组新增与编辑*/
            $edit_user = $rbac->getOneAdminUserRelationRole($request->input('admin_id'));
            if($edit_user->admin_role->is_super_management_group == Rbac::IS_SUPER_MANAGEMENT_GROUP  && $admin_u->admin_role->is_super_management_group != Rbac::IS_SUPER_MANAGEMENT_GROUP)
            {
                $m3result->code    = 2;
                $m3result->messages= '无法更改超级用户';
                return $m3result->toJson();
            }

            if($edit_user->admin_role->is_super_management_group == Rbac::IS_SUPER_MANAGEMENT_GROUP && $request->input('is_enable') == Rbac::MANAGER_NO_ENABLE)
            {
                $m3result->code    = 3;
                $m3result->messages= '超级用户无法被禁用';
                return $m3result->toJson();
            }

            /*过滤更新字段,只更新在$rules验证字段存在时的字段*/
            list($rules_keys) = array_divide($rules);
            $admin_user->where('admin_id', $request->input('admin_id'))->update(array_only($request->all(), $rules_keys));

            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
            Rbac::adminLog('ID:'.$request->input('admin_id'));
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failed');
            $m3result->data  = $validator->messages();
        }

        return $m3result->toJson();
    }

    /**
     * 用于在修改过路由的情况下,重新生成权限表
     * @return \App\Tools\json
     */
    public function PrivilegeUpdate()
    {
        /*初始化*/
        $rbac = new Rbac();
        $m3result = new M3Result();
        if($rbac->updateAdminPrivilege() == true)
        {
            $m3result->code = 0;
            $m3result->messages = __('admin.success');
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = __('admin.failed');
        }

        return $m3result->toJson();
    }
}