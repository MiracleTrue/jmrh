<?php

/**
 * Created by laravelShop.
 * Author: maChenChen QQ:482929305
 * Data: 2017/6/29
 * Time: 9:44
 */
namespace App\Models;

use App\Entity\EmailConfig;
use App\Entity\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Class Email 邮件有关的模型
 * @package App\Models
 */
class Email extends CommonModel
{
	private $errors;
	public function messages()
	{
		return $this->errors;
	}

   /**
    * 新增邮件服务器设置
    * @request
    * return bool;
    */
	public function AddAdminEmailServer($emailServer)
	{
		if($emailServer){
			$emailConfig = new EmailConfig();
			$emailConfig->merchant_id  = $emailServer['identity'];
			$emailConfig->smtp_server = $emailServer['smtpServer'];
			$emailConfig->port = $emailServer['port'];
			$emailConfig->email_from = $emailServer['emailFrom'];
			$emailConfig->password = $emailServer['emailPossword'] ;
			if($emailConfig->save())
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else{
			return false;
		}
	}
	/**
    * 编辑商家自营的邮件服务器设置
    * @request
    * return bool;
    */
	public function EditAdminEmailServer($emailServer)
	{
		$emailConfig = EmailConfig::find($emailServer['emailConfig_id']);
		if($emailConfig){
			$emailConfig->merchant_id  = $emailServer['identity'];
			$emailConfig->smtp_server = $emailServer['smtpServer'];
			$emailConfig->port = $emailServer['port'];
			$emailConfig->email_from = $emailServer['emailFrom'];
			$emailConfig->password = $emailServer['emailPossword'] ;
			if($emailConfig->save())
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else{
			return false;
		}
	}
	/**
	 * 编辑邮件模板
	 * @request 邮件模板参数
	 * return bool;
	 */
	public function EditAdminEmailTemplate($emailTemplate)
	{
		$template = EmailTemplate::where('template_code',$emailTemplate['template_code'])->first();
		if($template)
		{
			$template->merchant_id = $emailTemplate['merchant_id'];
			$template->template_subject = $emailTemplate['subject'];
			$template->template_content = $emailTemplate['content'];
			if($template->save())
			{
				return true;
			}
			else
			{
				return false;
			}
		}else
		{
			return false;
		}
	}
	/**
	 * 获取商家或入驻商家的邮件服务器配置
	 * @identity 0代表商家身份，其他数字代表入驻商家的id;
	 * return emailConfig;
	 */
	public function GetOneEmailConfigByMerchantId($merchant_id=0)
	{
		$emailConfig = EmailConfig::where('merchant_id',$merchant_id)->first();
		if($emailConfig)
		{
			$returnVal =  $emailConfig;
		}
		else
		{
			$returnVal = null;
		}
		return $returnVal;
	}

	/**
	 * 获取邮件模板列表
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function GetListTemplate()
	{
		$emailTemplate = EmailTemplate::all();
		return $emailTemplate;
	}

	/**
	 * 获取一条邮件模板通过主键id
	 * @param $template_id
	 * @return mixed
	 */
	public function GetOneTemplateByTemplateId($template_id)
	{
		$emailTemplate = EmailTemplate::find($template_id);
		return $emailTemplate;
	}

	/**
	 * 根据模板ID获取模板
	 * return emaiTemplate
	 */
	public function GetOneTemplateByCode($template_code)
	{
		$emailTemplate = EmailTemplate::where('template_code',$template_code)->first();
		return $emailTemplate;
	}
	/**
	 * 修改env中有关邮件的配置
	 * @request 发送邮件参数
	 * return bool;
	 */
	public function SetEmailEnv($configData)
	{
		if($configData)
		{
			config(['mail.host' => $configData->smtp_server]);
			config(['mail.port' => $configData->port]);
			config(['mail.username' => $configData->email_from]);
			config(['mail.password' => $configData->password]);
			config(['mail.from.address' => $configData->email_from]);
			config(['mail.from.name' => $configData->mailFromName]);
			config(['mail.encryption' => 'ssl']);
			return true;
		}
		else
		{
			return false;
		}
	}

	/*
	 * 发送邮件功能;
	 * $merchant_id 商家ID；$template_code 邮件模板code标识；$emailTo 收件人，$emailContentData 发送邮件模板内容变量
	 * return bool
	 */
	public function EmailSend($merchant_id,$template_code,$emailTo,$emailContentData)
	{
		//Log::useFiles(storage_path().'/logs/email.log');
		//获取商家邮箱配置
		$serverConfig = $this->GetOneEmailConfigByMerchantId($merchant_id);
		//根据商家id查询商家名称；此功能后续补充
		$shopName = 'laraver商城';
		$serverConfig['mailFromName'] = $shopName;
		//获取对应模板
		$emailTemplate = $this->GetOneTemplateByCode($template_code);
		$noteJson = $emailTemplate->notes;
		/* 解析json数据 */
		$noteArray=json_decode($noteJson,true);
        /* 验证数据 */
		$rules   = $noteArray['validate']['content'];
		if(is_array($rules))
		{
			$validator = Validator::make($emailContentData, $rules);
			if(!$validator->passes())
			{
				log::error('发送失败,模板code:'.$template_code.'发送至:'.$emailTo.',code:1,msg:数据验证失败');
				return false;
			}
		}
		/* 非必须数据，参数没有传过来的给默认值 */
		if(!isset($emailContentData['shop_name']))
		{
			$emailContentData['shop_name'] = $shopName;
		}
		if(!isset($emailContentData['send_date']))
		{
			$emailContentData['send_date'] = Carbon::now();
		}
		$templateSubject = $emailTemplate->template_subject;
		$msg = $this->ncReplaceText($emailTemplate->template_content,$emailContentData);
        if(!$msg)
        {
	        return false;
        }
		/* 发送邮件 */
		if($serverConfig != null)
		{  //发送邮件
			if($this->SetEmailEnv($serverConfig)) {
				try
				{
					$flag = Mail::send('admin.temp.email_send_template', ['content' => $msg], function ($message) use ($emailTo, $templateSubject) {
						$message->to($emailTo);
						$message->subject($templateSubject);
					});
					if ($flag == null) {
						//log::info('发送成功,模板code:' . $template_code . '发送至:' . $emailTo . ',code:0,msg:success');
						return true;
					}
					else
					{
						$this->errors = '未知错误';
						//log::error('发送失败,模板code:' . $template_code . '发送至:' . $emailTo . ',code:2,msg:未知错误');
						return false;
					}
				}
				catch (\Exception $e)
				{
					$this->errors = '邮件服务器配置错误';
					//log::error('发送失败,模板code:' . $template_code . '发送至:' . $emailTo . ',code:2,msg:邮件服务器配置错误');
					return false;
				}
			}
			else
			{
				$this->errors = '临时配置邮件服务器失败';
				//log::error('发送失败,模板code:'.$template_code.'发送至:'.$emailTo.',code:2,msg:临时配置邮件服务器失败');
				return false;
			}
		}
		else
		{
			$this->errors = '商家没有在邮件服务器设置下配置发送邮件所需参数';
			//log::error('发送失败,模板code:'.$template_code.'发送至:'.$emailTo.',code:2,msg:商家没有在邮件服务器设置下配置发送邮件所需参数');
			return false;
		}

	}

	/**
	 * 通知邮件/通知消息 内容转换函数
	 *
	 * @param string $message 内容模板
	 * @param array $param 内容参数数组
	 * @return string 通知内容
	 */
	function ncReplaceText($message,$param){
		if(!is_array($param))return false;
		foreach ($param as $k=>$v){
			$message	= str_replace('{$'.$k.'}',$v,$message);
		}
		return $message;
	}


}



