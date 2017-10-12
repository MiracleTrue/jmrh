<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 13:38
 */

namespace App\Models;

use App\Entity\UserLog;
use App\Entity\Users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * 用户相关模型
 * Class User
 * @package App\Models
 */
class User extends CommonModel
{
    /*禁用状态:  1.禁用  0.启用*/
    const IS_DISABLE = 1;
    const NO_DISABLE = 0;
    /*身份标识: 1.超级管理员  2.平台运营员 3.供货商  4.军方*/
    const ADMINISTRATOR = 1;
    const PLATFORM_ADMIN = 2;
    const SUPPLIER_ADMIN = 3;
    const ARMY_ADMIN = 4;

    private $errors = array(); /*错误信息*/

    public function getUserList()
    {
        /*初始化*/
        $e_users = new Users();

        /*预加载ORM对象*/
        $user_list = $e_users->orderBy('user_id', 'desc')->paginate($_COOKIE['PaginationSize']);

        /*数据过滤排版*/
        $user_list->transform(function ($item)
        {
//            if (empty($item->ho_admin_user))
//            {   /*如果用户不存在或已经被删除,删除其操作记录*/
//                AdminLog::destroy($item->log_id);
//            }
//            else
//            {
//                $item->log_info = CommonModel::languageFormat($item->zh_loginfo, $item->en_loginfo);
//                $item->created_at = Carbon::createFromTimestamp($item->created_at);
//                $item->admin_user = $item->ho_admin_user;
//                $item->admin_role = $item->ho_admin_user->ho_admin_role;
//                return $item;
//            }
        });

        return $user_list;
    }

    public function addUser($arr)
    {
        /*初始化*/
        $e_users = new Users();
        $password = new Password();

        /*添加用户*/
        $e_users->user_name = $arr['user_name'];
        $e_users->nick_name = $arr['nick_name'];
        $e_users->identity = $arr['identity'];
        $e_users->password = $password->makeHashPassword($arr['password']) or die();
        $e_users->phone = !empty($arr['phone']) ? $arr['phone'] : '';
        $e_users->is_disable = self::NO_DISABLE;
        $e_users->create_time = Carbon::now()->timestamp;

        $e_users->save();
        User::userLog(User::identityTransformText($e_users->identity) . ': ' . $e_users->nick_name . "($e_users->user_name)");
        return true;
    }


    /**
     * 用户的操作日志生成,请在模型中调用,并写入描述
     * @param string $desc 描述
     */
    public static function userLog($desc = '')
    {
        /*初始化*/
        $manage_u = session('ManageUser');
        $user_log = new UserLog();

        $route = Route::current();

        /*判断是合法的路由格式*/
        if (!empty($route->action['group']) && !empty($route->action['as']))
        {
            /*拼接log信息的样式*/
            $log_desc = $route->action['group'] . '-' . $route->action['as'] . ' >> ' . $desc;

            /*入库*/
            $user_log->user_id = $manage_u->user_id;
            $user_log->ip_address = \Illuminate\Support\Facades\Request::getClientIp();
            $user_log->log_desc = $log_desc;
            $user_log->create_time = Carbon::now()->timestamp;
            $user_log->save();
        }
    }

    /**
     * 返回用户身份标识的文本名称
     * @param $identity & 身份标识
     * @return string
     */
    public static function identityTransformText($identity)
    {
        $text = '';
        switch ($identity)
        {
            case self::SUPPLIER_ADMIN:
                $text = '供货商';
                break;
            case self::PLATFORM_ADMIN:
                $text = '平台运营员';
                break;
            case self::ARMY_ADMIN:
                $text = '军方';
                break;
            case self::ADMINISTRATOR:
                $text = '超级管理员';
                break;
        }
        return $text;
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