<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/15 0015
 * Time  : 16:02
 */

namespace App\Entity;

/**
 * Class AdminLog   数据库Eloquent实体模型
 * Table admin_log  后台系统管理员操作记录表
 * @package App\Entity
 */
class AdminLog extends CommonEntity
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'admin_log';

    /**
     * 可以通过 $primaryKey 属性，重新定义主键字段
     * @var string
     */
    protected $primaryKey = 'log_id';

    /**
     * 默认情况下，Eloquent预计数据表中有 "created_at" & "updated_at" 字段。
     * 不希望Eloquent字段维护这2个字段，可设置：$timestamps = false
     * @var bool
     */
    public $timestamps = false;

    /**
     * 一对一关联AdminUser实体表
     */
    public function ho_admin_user()
    {
        return $this->hasOne(AdminUser::class,'admin_id','admin_id');
    }

}
