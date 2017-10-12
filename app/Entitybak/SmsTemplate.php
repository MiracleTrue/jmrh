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
 * Class SmsTemplate 数据库实体模型
 * Table sms_template
 * @package App\Entity
 */
class SmsTemplate extends Model
{
	/**
	 * 与模型关联的数据表
	 *
	 * @var string
	 */
	protected $table = 'sms_template';

	/**
	 * 可以通过 $primaryKey 属性，重新定义主键字段
	 * @var string
	 */
	protected $primaryKey = 'template_id';

	/**
	 * 默认情况下，Eloquent预计数据表中有 "created_at" & "updated_at" 字段。
	 * 不希望Eloquent字段维护这2个字段，可设置：$timestamps = false
	 * @var bool
	 */
	public $timestamps = false;


}