<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/15 0015
 * Time  : 16:02
 */

namespace App\Entity;

/**
 * Class AdminUser   数据库Eloquent实体模型
 * Table admin_user  后台系统管理员表
 * @package App\Entity
 */
class AdminUser extends CommonEntity
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'admin_user';

    /**
     * 可以通过 $primaryKey 属性，重新定义主键字段
     * @var string
     */
    protected $primaryKey = 'admin_id';

    /**
     * 默认情况下，Eloquent预计数据表中有 "created_at" & "updated_at" 字段。
     * 不希望Eloquent字段维护这2个字段，可设置：$timestamps = false
     * @var bool
     */
    public $timestamps = true;

    /**
     * 需要自定义时间戳格式，可在模型内设置 $dateFormat 属性（决定了日期如何在数据库中存储，以及当模型被序列化成数组或JSON时的格式）
     * 格式为 date() 函数第一个参数，详情看手册
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * 一对一关联AdminRole实体表
     */
    public function ho_admin_role()
    {
        return $this->hasOne(AdminRole::class,'role_id','role_id');
    }


}
