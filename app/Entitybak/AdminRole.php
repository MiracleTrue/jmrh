<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/15 0015
 * Time  : 16:02
 */

namespace App\Entity;

/**
 * Class AdminRole   数据库Eloquent实体模型
 * Table admin_role  后台管理员角色表
 * @package App\Entity
 */
class AdminRole extends CommonEntity
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'admin_role';

    /**
     * 可以通过 $primaryKey 属性，重新定义主键字段
     * @var string
     */
    protected $primaryKey = 'role_id';

    /**
     * 默认情况下，Eloquent预计数据表中有 "created_at" & "updated_at" 字段。
     * 不希望Eloquent字段维护这2个字段，可设置：$timestamps = false
     * @var bool
     */
    public $timestamps = false;


    /**
     * 一对多关联AdminUser实体表
     */
    public function hm_admin_user()
    {
        return $this->hasMany(AdminUser::class,'role_id');
    }



}
