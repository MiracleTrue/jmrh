<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */

namespace App\Entity;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ProductCategory   数据库Eloquent实体模型
 * Table 商品分类表
 * @package App\Entity
 */
class ProductCategory extends CommonEntity
{
    /**
     * 数据模型的启动方法
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * 全局作用域
         * 商品分类删除状态
         * is_delete
         */
        static::addGlobalScope('is_delete', function (Builder $builder)
        {
            $builder->where('is_delete', Product::CATEGORY_NO_DELETE);
        });
    }

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'product_category';

    /**
     * 可以通过 $primaryKey 属性，重新定义主键字段
     * @var string
     */
    protected $primaryKey = 'category_id';

    /**
     * 默认情况下，Eloquent预计数据表中有 "created_at" & "updated_at" 字段。
     * 不希望Eloquent字段维护这2个字段，可设置：$timestamps = false
     * @var bool
     */
    public $timestamps = false;

    /**
     * 需要自定义时间戳格式，可在模型内设置 $dateFormat 属性（决定了日期如何在数据库中存储，以及当模型被序列化成数组或JSON时的格式）
     * 格式为 date() 函数第一个参数，详情看手册
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * 一对多关联Products实体表
     */
    public function hm_products()
    {
        return $this->hasMany(Products::class, 'category_id');
    }

    /**
     * 一对一关联Users实体表
     */
    public function ho_users()
    {
        return $this->hasOne(Users::class,'user_id','manage_user_id');
    }

//    /**
//     * 远程一对多关联Users实体表,中间模型ProductsCategoryManage
//     */
//    public function hmt_users()
//    {
//        return $this->hasManyThrough(Users::class, ProductsCategoryManage::class, 'category_id', 'user_id', 'category_id', 'user_id');
//    }

}
