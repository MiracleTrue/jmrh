<?php

/**
 * Created by laravelShop.
 * Author: maChenChen QQ:482929305
 * Data: 2017/6/29
 * Time: 9:50
 */
namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AdvertEntity 数据库Eloquent实体模型
 * Table advert
 * @package App\Entity
 */
class AdvertEntity extends Model
{
	/**
	 * 与模型关联的数据表
	 *
	 * @var string
	 */
	protected $table = 'advert';

	/**
	 * 可以通过 $primaryKey 属性，重新定义主键字段
	 * @var string
	 */
	protected $primaryKey = 'ad_id';

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
//	protected $dateFormat = 'U';
}

