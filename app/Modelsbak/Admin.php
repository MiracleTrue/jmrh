<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/18 0018
 * Time  : 14:17
 */

namespace App\Models;
use App\Entity\AdminLog;
use App\Entity\AdminUser;
use App\Entity\SystemConfig;
use Carbon\Carbon;

/**
 * Class Admin 后台未细节模块化的模型
 * @package App\Models
 */
class Admin extends CommonModel{

    private $errors =array(); /*错误信息*/

    /**
     * 用户登录检测与校验
     * @param $request
     * @return bool & User
     */
    public function userLoginCheck($request)
    {
        /*初始化*/
        $admin_user = new AdminUser();
        $password   = new Password();
        $rbac       = new Rbac();

        /*获得登录用户信息*/
        $user = $admin_user->where('admin_name',$request->input('admin_name'))->get()->first();

        if(!empty($user))
        {
            $user = $rbac->getOneAdminUserRelationRole($user->admin_id);/*获取单个管理员用户与对应角色的关联数据*/
            if($user->is_enable == Rbac::MANAGER_IS_ENABLE)
            {
                if($password->checkHashPassword($request->input('password') , $user->password) === true)
                {
                    /*验证成功,返回User对象*/
                    return $user;
                }
                else
                {
                    $this->errors['userLoginCheck']['code']     = 101;
                    $this->errors['userLoginCheck']['messages'] = '用户密码错误';
                    return false;
                }
            }
            else
            {
                $this->errors['userLoginCheck']['code']     = 102;
                $this->errors['userLoginCheck']['messages'] = '用户已停用';
                return false;
            }
        }
        else
        {
            $this->errors['userLoginCheck']['code']     = 103;
            $this->errors['userLoginCheck']['messages'] = '用户不存在';
            return false;
        }


    }

    /**
     * 用户登录成功的处理
     */
    public function userLoginSuccess($user)
    {
        /*加入merchant自营id*/
        $user->merchant_id = $this::SELF_EMPLOYED_ID;
        /*更新最后登录时间*/
        $admin_user = AdminUser::find($user->admin_id);
        $admin_user->last_login = Carbon::now()->timestamp;
        $admin_user->save();
        /*加入session*/
        session(['AdminUser' => $user]);
    }

    /**
     * 获取后台系统设置,按照表单属性排序的数据,用于页面显示
     * @param string $order_by
     * @return array
     */
    public function getSystemConfigToPage($order_by = 'asc')
    {
        /*初始化*/
        $arr        = array();
        $html_range = array();
        $system_config = new SystemConfig();

        $config_all = $system_config->where('parent_id',0)->orderBy('sort',$order_by)->get();
        foreach($config_all as $key => $vel)
        {
            $arr[$key] = $vel;
            $arr[$key]['child'] = $system_config->where('parent_id',$vel['id'])->orderBy('sort',$order_by)->get();

            foreach($arr[$key]['child'] as $child_key => $child_value)
            {
                /*单选框数据格式*/
                if($child_value['type'] == 'radio' && $child_value)
                {
                    /*解析数据库中选择范围的Json数据,并根据环境中英文转换*/
                    $temp_arr = json_decode($child_value['select_range'],true);

                    foreach($temp_arr as $row_key => $row_range)
                    {
                        $html_range[$row_key]['value'] = $row_range['value'];
                        $html_range[$row_key]['name']  = CommonModel::languageFormat($row_range['name'],$row_range['en_name']);
                    }
                }

                $arr[$key]['child'][$child_key]['html_range']= $html_range;
            }
        }

        return $arr;
    }


    /**
     * 获取所有系统设置,返回PHP一维关联数组
     * ['shop_name'=>'LaravelShop','shop_logo'=>'logo.png','shop_closed'=>1]
     * @return array
     */
    public function getSystemConfig()
    {
        $system_config = new SystemConfig();

        $config_all    = $system_config->where('parent_id','>',0)->get();//;->keyBy('name_code')->only('name_code','value');
        $config_return = $config_all->mapWithKeys(function ($item) {
            return [$item['name_code'] => $item['value']];
        });

        return $config_return->toArray();
    }

    /**
     * 后台 系统设置函数,传入Request,根据数据库中存放的设置字段更新Request含有相同字段的值
     * @param $request
     * @return bool
     */
    public function setSystemConfig($request)
    {
        /*初始化*/
        $system_config = new SystemConfig();
        $my_file       = new MyFile();
        $protective_field = ['shop_logo','shop_default_picture'];
        $auto_update   = $system_config->where('parent_id','>',0)->get();

        /*循环所有设置字段,将受保护数组中的字段去除,避免循环设置,及文件和特殊属性的单独设置*/
        foreach($auto_update as $key => $value)
        {
            if(in_array($value->name_code,$protective_field))
            {
               unset($auto_update[$key]);
            }
        }

        /*网站logo*/
        if($request->hasFile('shop_logo'))
        {
            $my_file->deleteFile($system_config->where('name_code','shop_logo')->get()->first()->value);/*删除原文件*/
            $path = $my_file->uploadOriginal($request->file('shop_logo') , '/' ,'logo');/*上传成功*/
            $system_config->where('name_code', 'shop_logo')->update(['value' => $path]);/*保存数据库*/
        }

        /*商品与文章默认图片*/
        if($request->hasFile('shop_default_picture'))
        {
            $my_file->deleteFile($system_config->where('name_code','shop_default_picture')->get()->first()->value);/*删除原文件*/
            $path = $my_file->uploadThumb($request->file('shop_default_picture') , '/' , 'default_picture');/*上传成功*/
            $system_config->where('name_code', 'shop_default_picture')->update(['value' => $path]);/*保存数据库*/
        }

        /*循环所有设置字段*/
        foreach($auto_update as $value)
        {
            if($request->has($value->name_code) && $value->value != $request->input($value->name_code))/*判断如果非空并且和原本值不一致,更新数据库*/
            {
                $system_config->where('name_code', $value->name_code )->update(['value' => $request->input($value->name_code)]);/*保存数据库*/
            }
        }
        Rbac::adminLog('修改系统设置');

        return true;
    }

    /**
     * 获取自营店铺信息
     * @return \Illuminate\Support\Collection
     */
    public function getAdminMerchantInfo()
    {
        $collection = collect(['merchant_id' => $this::SELF_EMPLOYED_ID, 'user_id' => 0, 'shop_name' => $GLOBALS['shop_config']['self_employed_name']]);

        return $collection;
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