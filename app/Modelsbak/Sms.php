<?php
/**
 * Created by laravelShop.
 * Author: maChenChen QQ:482929305
 * Data: 2017/6/29
 * Time: 10:10
 */
namespace App\Models;

use App\Entity\SmsConfig;
use App\Entity\SmsTemplate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * Class Sms 短信有关的模型
 * @package App\Models
 */

class Sms extends CommonModel
{
	private $errors;
	/**
	 * 返回 模型 发生的错误信息
	 * @return mixed
	 */
	public function messages()
	{
		return $this->errors;
	}
	/**
	 * 新增短信配置
	 * @param $sms_config
	 * @return bool
	 */
	public function AddAdminSmsConfig($sms_config)
	{
		$smsConfig = new SmsConfig();
		$smsConfig->sms_type = $sms_config['sms_type'];
		$smsConfig->merchant_id = $sms_config['merchant_id'];
		$smsConfig->appkey = $sms_config['appkey'];
		$smsConfig->secretKey = $sms_config['secretKey'];
		$smsConfig->signName = $sms_config['signName'];
		if($smsConfig->save())
		{
			Rbac::adminLog('新增短信配置'."签名：$smsConfig->signName"."($smsConfig->sms_id)");
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 获取商家短信配置信息（默认获取管理员的）
	 * @param $merchant_id
	 * @return mixed
	 */
	public function GetOneSmsConfigByMerchantId($merchant_id=0)
	{
		$smsConfig = SmsConfig::where('merchant_id',$merchant_id)->first();
		return $smsConfig;
	}

	/**
	 * 编辑短信配置
	 * @param $sms_config
	 * @return bool
	 */
	public function EditAdminSmsConfig($sms_config)
	{
		$smsConfig = SmsConfig::find($sms_config['sms_id']);
		$smsConfig->sms_type = $sms_config['sms_type'];
		$smsConfig->merchant_id = $sms_config['merchant_id'];
		$smsConfig->appkey = $sms_config['appkey'];
		$smsConfig->secretKey = $sms_config['secretKey'];
		$smsConfig->signName = $sms_config['signName'];
		if($smsConfig->save())
		{
			Rbac::adminLog('编辑短信配置'."签名：$smsConfig->signName"."($smsConfig->sms_id)");
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 获取商家短信模板（默认获取管理员的）
	 * @param int $merchant_id
	 * @return mixed
	 */
	public function GetListSmsTemplateByMerchantId($merchant_id = 0)
	{
		$smsTemplate = SmsTemplate::where('merchant_id',$merchant_id)->paginate($_COOKIE['AdminPaginationSize']);
		return $smsTemplate;
	}

	/**
	 * 短信模板模糊查询
	 * @param $keywords
	 * @return mixed
	 */
	public function GetSmsTemplateFuzzyQuery($keywords)
	{
		$smsTemplate = SmsTemplate::where('template_name','like','%'.$keywords.'%')->orwhere('template_code','like','%'.$keywords.'%')
			->orwhere('aliyu_code','like','%'.$keywords.'%')->orwhere('template_content','like','%'.$keywords.'%')->paginate($_COOKIE['AdminPaginationSize']);
		return $smsTemplate;
	}

	/**
	 * 获取一条短信模板根据短信模板id
	 * @param $template_id
	 * @return mixed
	 */
	public function GetOneSmsTemplateByTemplateId($template_id)
	{
		$smsTemplate = SmsTemplate::find($template_id);
		return $smsTemplate;
	}

	/**
	 * 新增短信模板
	 * @param $sms_template
	 * @return bool
	 */
	public function AddAdminSmsTemplate($sms_template)
	{
		$smsTemplate = new SmsTemplate();
		$smsTemplate->merchant_id = $sms_template['merchant_id'];
		$smsTemplate->template_name = $sms_template['template_name'];
		$smsTemplate->template_code = $sms_template['template_code'];
		$smsTemplate->aliyu_code = $sms_template['aliyu_code'];
		$smsTemplate->template_content = $sms_template['template_content'];
		if($smsTemplate->save())
		{
			Rbac::adminLog('新增短信模板:'.$smsTemplate->template_code."($smsTemplate->template_id)");
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 编辑短信模板
	 * @param $sms_template
	 * @return bool
	 */
	public function EditAdminSmsTemplate($sms_template)
	{
		$smsTemplate = SmsTemplate::find($sms_template['template_id']);
		$smsTemplate->merchant_id = $sms_template['merchant_id'];
		$smsTemplate->template_name = $sms_template['template_name'];
		$smsTemplate->template_code = $sms_template['template_code'];
		$smsTemplate->aliyu_code = $sms_template['aliyu_code'];
		$smsTemplate->template_content = $sms_template['template_content'];
		if($smsTemplate->save())
		{
			Rbac::adminLog('编辑短信模板:'.$smsTemplate->template_code."($smsTemplate->template_id)");
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 删除模板根据模板ID
	 * @param $template_id
	 * @return bool
	 */
	public function DeleteOneAdminSmsTemplate($template_id)
	{
		$smsTemplate = SmsTemplate::find($template_id);
		if($smsTemplate->delete())
		{
			Rbac::adminLog('删除短信模板:'.$smsTemplate->template_code."($smsTemplate->template_id)");
			return true;
		}
		else
		{
			return false;
		}

	}

	/**
	 * 获取一条短信模板根据模板code
	 * @param $aliyu_code
	 * @return mixed
	 */
	public function GetOneSmsTemplateByTemplateCode($aliyu_code)
	{
		$smsTemplate = SmsTemplate::where('aliyu_code',$aliyu_code)->first();
		return $smsTemplate;
	}

	/**
	 * 判断新增的短信编码是否唯一
	 * @param $template_code
	 * @return bool
	 */
	public function SmsTemplateCodeIsUnique($template_code)
	{
		$smsTemplate = SmsTemplate::where('template_code',$template_code)->get();
		if(count($smsTemplate) == 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 发送短信
	 * @param $appKey
	 * @param $secretKey
	 * @param $signName
	 * @param $aliyuCode
	 * @param $recName
	 * @param $parameter
	 * @return mixed|\ResultSet|\SimpleXMLElement
	 */
	public function SendMsm($appKey,$secretKey,$signName,$aliyuCode,$templateCode,$recName,$parameter)
	{
		Log::useFiles(storage_path().'/logs/sms.log');
		/*验证数据*/
		$smsTemplate = $this->GetOneSmsTemplateByTemplateCode($aliyuCode);
		$notes = json_decode($smsTemplate['notes'],true);
		$rules   = $notes['validate']['content'];
		if(is_array($rules))
		{
			$validator = Validator::make($parameter,$rules);
			/*验证失败*/
			if($validator->fails())
			{
				log::error('发送失败,模板code:'.$templateCode.'发送至:'.$recName.',code:1,msg:数据验证失败');
				$this->errors = '数据验证失败';
				return false;
			}
		}
		$param = json_encode($parameter);
		/*发送短信*/
		include (app_path() . '\Plugins\alidayu\TopSdk.php');
		date_default_timezone_set('Asia/Shanghai');
		$c = new \TopClient;
		$c->appkey = $appKey;
		$c->secretKey = $secretKey;
		$req = new \AlibabaAliqinFcSmsNumSendRequest;
		$req->setExtend("123456");
		$req->setSmsType("normal");
		$req->setSmsFreeSignName($signName);
		$req->setSmsParam($param);
		$req->setRecNum($recName);
		$req->setSmsTemplateCode($aliyuCode);
		$resp = $c->execute($req);
		if($resp->result->err_code==0 && $resp->result->success=="true")
		{
			log::info('发送成功,模板code:'.$templateCode.'发送至:'.$recName.',code:0,msg:success');
			return true;
		}
		elseif($resp->code==29 || $resp->code==25)
		{
			log::error('发送失败,模板code:'.$templateCode.'发送至:'.$recName.',code:'.$resp->code.',msg:appkey或screatKey错误');
			$this->errors = 'appkey或screatKey错误';
			return false;

		}
		elseif($resp->code==15)
		{
			log::error('发送失败,模板code:'.$templateCode.'发送至:'.$recName.',code:'.$resp->code.',msg:'.$resp->sub_msg);
			$this->errors = $resp->sub_msg;
			return false;

		}
		else
		{
			log::error('发送失败,模板code:'.$templateCode.'发送至:'.$recName.',code:'.$resp->code.',msg:未知错误');
			$this->errors = '未知错误';
			return false;
		}
	}

	/**
	 * 取出邮箱模板中的变量转变成json格式验证
	 * @param $template_content
	 * @return string
	 */
	public function SmsTemplateContentParamToJson($template_content)
	{
		$arr=[];
		preg_match_all('/\$\{([^\}$]+)/',$template_content,$arr,PREG_PATTERN_ORDER);
		if(empty($arr[0]))
		{
			$json = null;
		}
		else
		{
			foreach($arr[1] as $item)
			{
				$rules[$item] = 'required';
			}
			$json = json_encode($rules);
		}
		return $json;
	}



}