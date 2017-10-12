<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/18 0018
 * Time  : 14:17
 */

namespace App\Models;

use App\Entity\AdminLog;
use App\Entity\AdminRole;
use App\Entity\AdminUser;
use App\Entity\AdminPrivilege;
use App\Tools\M3Result;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;


/**
 * Class Rbac 基于角色的访问控制 模型
 * @package App\Models
 */
class Rbac extends CommonModel{

    const IS_SUPER_MANAGEMENT_GROUP = 1;/*是   超级管理组*/

    const NO_SUPER_MANAGEMENT_GROUP = 0;/*不是 超级管理组*/

    const MANAGER_IS_ENABLE = 1; /*管理员状态  启用*/

    const MANAGER_NO_ENABLE = 0; /*管理员状态  禁用*/


    private $errors = array(); /*错误信息*/

    /**
     * Admin管理员的操作日志生成,请在控制器中调用,并写入中文和英文的额外数据
     * @param string $zh_extra 中文的额外数据
     * @param string $en_extra 英文的额外数据
     */
    public static function adminLog($zh_extra = '' , $en_extra = '')
    {
        /*初始化*/
        $admin_u = session('AdminUser');
        $admin_log = new AdminLog();
        $route = Route::current();

        /*判断是合法的路由格式*/
        if(!empty($route->action['group']) && !empty($route->action['action_group']) && !empty($route->action['as']))
        {
            /*拼接log信息的样式*/
            $zh_log = explode(',',$route->action['group'])[0].' - '.explode(',',$route->action['action_group'])[0].' - '.explode(',',$route->action['as'])[0].'>>'.$zh_extra;
            if(!empty($en_extra))
            {
                $en_log = explode(',',$route->action['group'])[1].' - '.explode(',',$route->action['action_group'])[1].' - '.explode(',',$route->action['as'])[1].'>>'.$en_extra;
            }
            else
            {
                $en_log = explode(',',$route->action['group'])[1].' - '.explode(',',$route->action['action_group'])[1].' - '.explode(',',$route->action['as'])[1].'>>'.$zh_extra;
            }

            /*入库*/
            $admin_log->admin_id = $admin_u->admin_id;
            $admin_log->ip_address = \Illuminate\Support\Facades\Request::getClientIp();
            $admin_log->zh_loginfo = $zh_log;
            $admin_log->en_loginfo = $en_log;
            $admin_log->created_at = Carbon::now()->timestamp;
            $admin_log->save();
        }

    }
    /**
     * 删除一个Admin角色 角色下的管理员会一并删除
     * @param $role_id
     * @return bool
     */
    public function deleteAdminRole($role_id)
    {
        /*初始化*/
        $admin_u = session('AdminUser');
        $edit_role  = $this->getOneAdminRoleRelationUser($role_id);

        /*无法删除超级用户*/
        if($edit_role->is_super_management_group == Rbac::IS_SUPER_MANAGEMENT_GROUP)
        {
            $this->errors['addAdminUser'][] = '无法更改超级用户';
            return false;
        }

        foreach($edit_role['admin_users'] as $value)
        {
            $admin_user = AdminUser::find($value->admin_id);

            $admin_user->delete();
        }

        $admin_role = AdminRole::find($role_id);
        $admin_role->delete();
        Rbac::adminLog($admin_role->role_name."($admin_role->role_id)");

        return true;
    }

    /**
     * 删除一个Admin管理员
     * @param $admin_id
     * @return bool
     */
    public function deleteAdminUser($admin_id)
    {
        /*初始化*/
        $admin_u = session('AdminUser');
        $edit_user = $this->getOneAdminUserRelationRole($admin_id);

        /*无法删除超级用户*/
        if($edit_user->admin_role->is_super_management_group == Rbac::IS_SUPER_MANAGEMENT_GROUP)
        {
            $this->errors['addAdminUser'][] = '无法更改超级用户';
            return false;
        }

        $admin_user = AdminUser::find($admin_id);
        $admin_user->delete();
        Rbac::adminLog($admin_user->admin_name."($admin_user->admin_id)");

        return true;
    }
    
    /**
     * 更新一个Admin后台管理员
     * @param $request
     * @return bool
     */
    public function editAdminUser($request)
    {
        /*初始化*/
        $admin_user = new AdminUser();
        $password   = new Password();
        $admin_u    = session('AdminUser');
        $user       = $admin_user->find($request->input('admin_id'));
        $edit_user  = $this->getOneAdminUserRelationRole($request->input('admin_id'));

        /*普通管理员无法对超级管理组新增与编辑*/
        if($edit_user->admin_role->is_super_management_group == Rbac::IS_SUPER_MANAGEMENT_GROUP  && $admin_u->admin_role->is_super_management_group != Rbac::IS_SUPER_MANAGEMENT_GROUP)
        {
            $this->errors['editAdminUser'][] = '无法更改超级用户';
            return false;
        }

        if($edit_user->admin_role->is_super_management_group == Rbac::IS_SUPER_MANAGEMENT_GROUP && $admin_u->admin_role->is_super_management_group != Rbac::IS_SUPER_MANAGEMENT_GROUP)
        {
            $this->errors['editAdminUser'][] = '无法更改超级用户';
            return false;
        }

        if($edit_user->admin_role->is_super_management_group == Rbac::IS_SUPER_MANAGEMENT_GROUP && $request->input('is_enable') == self::MANAGER_NO_ENABLE)
        {
            $this->errors['editAdminUser'][] = '超级用户无法被禁用';
            return false;
        }

        /*编辑管理员*/
        $user->admin_name = $request->input('admin_name');
        $user->role_id = $request->input('role_id');
        $user->password = $password->makeHashPassword($request->input('password')) or die();
        $user->phone = $request->input('phone');
        $user->email = $request->input('email');
        $user->last_ip = $request->getClientIp();
        $user->is_enable = $request->input('is_enable');

        $user->save();
        Rbac::adminLog('修改管理员:'.$user->admin_name."($user->admin_id)");
        return true;
    }
    /**
     * 添加一个Admin后台管理员
     * @param $request
     * @return bool
     */
    public function addAdminUser($request)
    {
        /*初始化*/
        $admin_user = new AdminUser();
        $password   = new Password();
        $admin_u    = session('AdminUser');
        $add_role   = AdminRole::findOrFail($request->input('role_id'));

        /*普通管理员无法对超级管理组新增与编辑*/
        if($add_role->is_super_management_group == Rbac::IS_SUPER_MANAGEMENT_GROUP  && $admin_u->admin_role->is_super_management_group != Rbac::IS_SUPER_MANAGEMENT_GROUP)
        {
            $this->errors['addAdminUser'][] = '无法更改超级用户';
            return false;
        }
        /*添加管理员*/
        $admin_user->admin_name = $request->input('admin_name');
        $admin_user->role_id = $add_role->role_id;
        $admin_user->password = $password->makeHashPassword($request->input('password')) or die();
        $admin_user->phone = $request->input('phone');
        $admin_user->email = $request->input('email');
        $admin_user->last_ip = $request->getClientIp();
        $admin_user->is_enable = $request->input('is_enable');

        $admin_user->save();
        Rbac::adminLog('新增管理员:'.$admin_user->admin_name."($admin_user->admin_id)");
        return true;
    }

    /**
     * 更新一个Admin后台角色
     * @param $request
     * @return bool
     */
    public function editAdminRole($request)
    {
        /*初始化*/
        $admin_role = new AdminRole();
        $admin_u    = session('AdminUser');
        $edit_role  = $admin_role->find($request->input('role_id'));

        if(!empty($edit_role))
        {
            /*普通管理员无法对超级管理组新增与编辑*/
            if($edit_role->is_super_management_group == Rbac::IS_SUPER_MANAGEMENT_GROUP  && $admin_u->admin_role->is_super_management_group != Rbac::IS_SUPER_MANAGEMENT_GROUP)
            {
                $this->errors['editAdminRole'][] = '无法更改超级用户';
                return false;
            }

            /*是否更新的是超级管理员*/
            if($request->has('is_super_management_group') && $request->input('is_super_management_group') == Rbac::IS_SUPER_MANAGEMENT_GROUP)
            {
                /*普通管理员无法对超级管理组新增与编辑*/
                if($admin_u->admin_role->is_super_management_group != Rbac::IS_SUPER_MANAGEMENT_GROUP)
                {
                    $this->errors['editAdminRole'][] = '无法更改超级用户';
                    return false;
                }
                $edit_role->role_name = $request->input('role_name');
                $edit_role->role_description = $request->has('role_description')  ? $request->input('role_description') : '';
                $edit_role->role_action = '*';
                $edit_role->is_super_management_group = Rbac::IS_SUPER_MANAGEMENT_GROUP;
                $edit_role->save();

                return true;
            }
            elseif($request->has('is_super_management_group') && $request->input('is_super_management_group') == Rbac::NO_SUPER_MANAGEMENT_GROUP)
            {
                /*格式化路由数组转换的字符串*/
                $action_str = $request->has('checked_action') ? implode(',',$request->input('checked_action')) : '';

                $edit_role->role_name = $request->input('role_name');
                $edit_role->role_description = $request->has('role_description')  ? $request->input('role_description') : '';
                $edit_role->role_action = $action_str;
                $edit_role->is_super_management_group = Rbac::NO_SUPER_MANAGEMENT_GROUP;
                $edit_role->save();
                Rbac::adminLog('修改角色:'.$edit_role->role_name."($edit_role->role_id)");

                return true;
            }
            else
            {
                $this->errors['editAdminRole'][] = '未知错误!';
                return false;
            }
        }
        else
        {
            $this->errors['editAdminRole'][] = '数据输入不完整!';
            return false;
        }
    }

    /**
     * 添加一个Admin后台角色
     * @param $request
     * @return bool
     */
    public function addAdminRole($request)
    {
        /*初始化*/
        $admin_role = new AdminRole();
        $admin_u    = session('AdminUser');

        /*是否添加的是超级管理员*/
        if($request->has('is_super_management_group') && $request->input('is_super_management_group') == Rbac::IS_SUPER_MANAGEMENT_GROUP)
        {
            /*普通管理员无法对超级管理组新增与编辑*/
            if($admin_u->admin_role->is_super_management_group != Rbac::IS_SUPER_MANAGEMENT_GROUP)
            {
                $this->errors['addAdminRole'][] = '无法更改超级用户';
                return false;
            }
            $admin_role->role_name = $request->input('role_name');
            $admin_role->role_description = $request->has('role_description')  ? $request->input('role_description') : '';
            $admin_role->role_action = '*';
            $admin_role->is_super_management_group = Rbac::IS_SUPER_MANAGEMENT_GROUP;
            $admin_role->save();

            return true;
        }
        elseif($request->has('is_super_management_group') && $request->input('is_super_management_group') == Rbac::NO_SUPER_MANAGEMENT_GROUP)
        {
            /*格式化路由数组转换的字符串*/
            $action_str = $request->has('checked_action') ? implode(',',$request->input('checked_action')) : '';

            $admin_role->role_name = $request->input('role_name');
            $admin_role->role_description = $request->has('role_description')  ? $request->input('role_description') : '';
            $admin_role->role_action = $action_str;
            $admin_role->is_super_management_group = Rbac::NO_SUPER_MANAGEMENT_GROUP;
            $admin_role->save();
            Rbac::adminLog('新增角色:'.$admin_role->role_name."($admin_role->role_id)");

            return true;
        }
        else
        {
            $this->errors['addAdminRole'][] = '数据输入不完整';
            return false;
        }
    }

    /**
     * 获取单个管理员用户与对应角色的关联数据
     * @param $admin_id
     * @return mixed
     */
    public function getOneAdminUserRelationRole($admin_id)
    {
        /*初始化*/
        $admin_user = new AdminUser();

        /*查询*/
        $user = $admin_user->findOrFail($admin_id);

        /*数据过滤*/
        $user->admin_role = $user->ho_admin_role;
        unset($user->ho_admin_role);

        return $user;
    }

    /**
     * 获取单个角色与对应多个管理员用户的关联数据
     * @param $role_id
     * @return mixed
     */
    public function getOneAdminRoleRelationUser($role_id)
    {
        /*初始化*/
        $admin_role = new AdminRole();

        /*查询*/
        $role = $admin_role->findOrFail($role_id);

        /*数据过滤*/
        $role->admin_users = $role->hm_admin_user;
        unset($role->hm_admin_user);

        return $role;
    }
    
    /**
     * 获取管理员角色与对应管理员用户的关联数据列表
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAdminRoleRelationUser()
    {
        /*初始化*/
        $admin_role = new AdminRole();

        /*查询*/
        $role = $admin_role->with('hm_admin_user')->get();

        /*数据过滤*/
        $role->transform(function($item){
            $item->admin_user = $item->hm_admin_user;
            unset($item->hm_admin_user);
            return $item;
        });

        return $role;

    }

    /**
     * 获取管理员用户与对应角色的关联数据列表
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAdminUserRelationRole()
    {
        /*初始化*/
        $admin_user = new AdminUser();

        /*查询*/
        $user_list = $admin_user->with('ho_admin_role')->get();
        $user_list->transform(function($item)
        {
            $item->admin_role = $item->ho_admin_role;
            unset($item->ho_admin_role);
            return $item;
        });

        return $user_list;
    }

    /**
     * 检查管理员是否有权限访问
     * @return bool
     */
    public function checkAdminPrivilege()
    {
        /*初始化*/
        $admin_u = session()->get('AdminUser');
        $route   = Route::current();/*当前路由对象*/
        /*转换数组*/
        $privilege_arr = explode(',',$admin_u->admin_role->role_action);

        /*判断*/
        if($admin_u->admin_role->is_super_management_group == self::IS_SUPER_MANAGEMENT_GROUP)/*超级管理员不限制*/
        {
            return true;
        }
        elseif(empty($route->action['group']) && empty($route->action['as']))/*排除没有分组且没有命名的路由*/
        {
            return true;
        }
        elseif(in_array($route->uri , $privilege_arr))
        {
            return true;
        }
        else
        {
            return false;
        }

    }
    /**
     * 获取Admin后台所有权限 如果传入角色权限则会自动返回选中状态 三级分类对象(中英文转换完成)
     * @param bool $role_privilege 角色权限
     * @return mixed
     */

    public function getAdminPrivilege($role_privilege = false)
    {
        /*初始化*/
        $system_privilege = new AdminPrivilege();
        $arr = $system_privilege->where('parent_id','0')->get();
        $role_privilege_arr = array();

        /*判断有没有传入角色权限,生成权限列表的选中状态*/
        if($role_privilege)
        {
            $role_privilege_arr = explode(',',$role_privilege);
        }

        /*生成三级分类格式*/
        $arr->transform(function($item) use($role_privilege,$role_privilege_arr)/*顶级组*/
        {
            $item->name = CommonModel::languageFormat($item->privilege_name , $item->privilege_en_name);
            $item->action_group = AdminPrivilege::where('parent_id' , $item->privilege_id)->get();


            $item->action_group->transform(function($action_group) use($role_privilege,$role_privilege_arr)/*路由组*/
            {
                $action_group->name = CommonModel::languageFormat($action_group->privilege_name , $action_group->privilege_en_name);
                $action_group->action = AdminPrivilege::where('parent_id' , $action_group->privilege_id)->get();

                $action_group->action->transform(function($row) use($role_privilege,$role_privilege_arr)/*单条请求*/
                {
                    $row->name = CommonModel::languageFormat($row->privilege_name , $row->privilege_en_name);
                    /*判断有没有传入角色权限,生成权限列表的选中状态*/
                    if($role_privilege && in_array($row->privilege_url ,$role_privilege_arr))
                    {
                        $row->checked = 'checked';
                    }
                    return $row;
                });
                return $action_group;
            });
            return $item;
        });

        return $arr;
    }

    /**
     * 更新admin后台权限列表,根据路由更新admin_privilege表
     */
    public function updateAdminPrivilege()
    {
        /*初始化变量*/
        $admin_arr     = array();
        $group_arr     = array();
        $routes        = app()->routes->getRoutes();

        AdminPrivilege::truncate();/*清空表*/

        /*查找出属于后台操作的路由arr*/
        foreach($routes as $key => $value)
        {
            if($value->action['namespace'] == 'App\Http\Controllers\Admin')
            {
                $admin_arr[] = $value;
            }
        }

        /*查找出按路由分组后的arr,如果单条路由不指定->name()属性,则不需要通过权限验证*/
        foreach($admin_arr as $key => $value)
        {
            if(!empty($value->action['group']) && !empty($value->action['as']))
            {
                $group_arr[$value->action['group']][$value->action['action_group']][] = $value;
            }
        }

        /*根据用户组入库,请求划分到组内,三级权限分录*/
        /*添加路由组的分组*/
        foreach($group_arr as $group_key => $action_group)
        {

            $system_privilege = new AdminPrivilege();
            $name_arr = explode(',',$group_key);/*$name_arr[0]中文  $name_arr[1]英文*/
            $system_privilege->parent_id = 0;
            $system_privilege->privilege_name = $name_arr[0];
            $system_privilege->privilege_en_name = $name_arr[1];
            $system_privilege->save();
            $parent_id = $system_privilege->privilege_id;

            /*添加路由请求组*/
            foreach($action_group as $action_group_key => $action)
            {
                $system_privilege = new AdminPrivilege();
                $name_arr = explode(',',$action_group_key);/*$name_arr[0]中文  $name_arr[1]英文*/
                $system_privilege->parent_id = $parent_id;
                $system_privilege->privilege_name = $name_arr[0];
                $system_privilege->privilege_en_name = $name_arr[1];
                $system_privilege->save();
                $parent_group = $system_privilege->privilege_id;

                /*添加单条路由请求*/
                foreach($action as $item)
                {
                    $system_privilege = new AdminPrivilege();
                    $name_arr = explode(',',$item->action['as']);/*$name_arr[0]中文  $name_arr[1]英文*/
                    $system_privilege->parent_id = $parent_group;
                    $system_privilege->privilege_url = $item->uri;
                    $system_privilege->privilege_controller = $this->controllerStringConversion($item->action['controller']);
                    $system_privilege->privilege_name = $name_arr[0];
                    $system_privilege->privilege_en_name = $name_arr[1];
                    $system_privilege->save();
                }
            }
        }
        Rbac::adminLog('更新系统角色权限');

        return true;
    }
    
    /**
     * 根据请求方式,返回不同的"没有"权限的信息
     * @param $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function noPrivilegePrompt($request)
    {
        if($request->method() == 'GET')
        {
            return redirect(action('Admin\IndexController@NoPrivilege'));
        }
        elseif($request->method() == 'POST')
        {

            $m3result = new M3Result();
            $m3result->code     = -1;
            $m3result->messages = __('admin.noPrivilege');
            die($m3result->toJson());
        }
        else
        {
            return redirect(action('Admin\IndexController@NoPrivilege'));
        }
    }

    /**
     * 获取Admin后台系统操作的log记录(如有admin_id则获取指定用户记录)"分页,语言"
     * @param int $admin_id
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAdminLogList($admin_id = 0)
    {
        /*初始化*/
        $admin_log = new AdminLog();

        /*预加载ORM对象*/
        if($admin_id > 0)
        {
            $log_list = $admin_log->with('ho_admin_user','ho_admin_user.ho_admin_role')->orderBy('created_at','desc')->where('admin_id' , $admin_id)->paginate($_COOKIE['AdminPaginationSize']);

        }
        else
        {
            $log_list = $admin_log->with('ho_admin_user','ho_admin_user.ho_admin_role')->orderBy('created_at','desc')->paginate($_COOKIE['AdminPaginationSize']);
        }

        /*数据过滤排版*/
        $log_list->transform(function($item)
        {
            if(empty($item->ho_admin_user))
            {   /*如果用户不存在或已经被删除,删除其操作记录*/
                AdminLog::destroy($item->log_id);
            }
            else
            {
                $item->log_info   = CommonModel::languageFormat($item->zh_loginfo,$item->en_loginfo);
                $item->created_at = Carbon::createFromTimestamp($item->created_at);
                $item->admin_user = $item->ho_admin_user;
                $item->admin_role = $item->ho_admin_user->ho_admin_role;
                return $item;
            }

        });

        return $log_list;

    }

    /**
     *  批量删除Admin管理员操作log
     * @param $delete_id_arr & log_id的数组
     * @return bool
     */
    public function batchDeleteAdminLog($delete_id_arr)
    {
        $admin_log = new AdminLog();

        $admin_log->whereIn('log_id', $delete_id_arr)->delete();

        Rbac::adminLog('删除操作日志:'.implode(',',$delete_id_arr));
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