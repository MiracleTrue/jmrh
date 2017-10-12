<?php
/**
 * Created by LaravelShop.
 * Author: maChenChen  QQ:482929305
 * Date  : 2017/5/15 0015
 * Time  : 16:02
 */
namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Tools\M3Result;
use Illuminate\Support\Facades\Validator;
use App\Models\Sms;

/**
 * Class 后台 基类控制器
 */
class SmsController extends CommonController
{
	public $ViewData = array(); /*传递页面的数组*/

	/**
	 * 短信设置
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function SettingView()
	{
        $sms = new Sms();
		$this->ViewData['sms_config'] = $sms->GetOneSmsConfigByMerchantId();
		$this->ViewData['nav_position'] = Menu::getAdminPosition();
		return view('admin.sms_setting_edit',$this->ViewData);
	}

	/**
	 * 编辑短信设置
	 * @param Request $request
	 * @return \App\Tools\json
	 */
	public function SettingEditSubmit(Request $request)
	{
		$m3_result = new M3Result();
		$rules = [
			'merchant_id'=>'required|integer|min:0',
			'sms_id'=>'required|integer|min:0',
			'sms_type' => 'required|integer',
			'appkey'     => 'required',
			'secretKey'     => 'required',
			'signName'     => 'required|min:2|max:16',
		];
		$validator = Validator::make($request->all(), $rules);
		if(!$validator->passes())
		{ /*验证失败*/
			$m3_result->code = 1;
			$m3_result->messages = __('admin.failed');
		}
		else
		{
			$sms = new Sms();
			/* 新增短信配置 */
			if($request->sms_id == 0)
			{
				$smsConfig = $sms->AddAdminSmsConfig($request);
			}
			/* 编辑短信配置 */
			else
			{
				$smsConfig = $sms->EditAdminSmsConfig($request);
			}
			if($smsConfig == true)
			{
				$m3_result->code = 0;
				$m3_result->messages = __('admin.success');
			}
			else
			{
				$m3_result->code = 2;
				$m3_result->messages = __('admin.failed');
			}
		}
		return $m3_result->toJson();
	}

	/**
	 * 短信模板列表
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function TemplateIndex()
	{
		$sms = new Sms();
		$this->ViewData['sms_template'] = $sms->GetListSmsTemplateByMerchantId();
		$this->ViewData['nav_position'] = Menu::getAdminPosition();
		return view('admin.sms_template_list',$this->ViewData);
	}

	public function TemplateFuzzyQuery(Request $request)
	{
		//$data = $request->all();
		$sms = new Sms();
		$this->ViewData['sms_template'] = $sms->GetSmsTemplateFuzzyQuery($request->fuzzy_query);
		$this->ViewData['nav_position'] = Menu::getAdminPosition();
		return view('admin.sms_template_list',$this->ViewData);
	}

	/**
	 * 模板设置页
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function TemplateView($template_id=0)
	{
		$sms = new Sms();
		$this->ViewData['nav_position'] = Menu::getAdminPosition();
		$this->ViewData['sms_template'] = null;
		if($template_id == 0)
		{
			return view('admin.sms_template_edit',$this->ViewData);
		}
		else
		{
			$sms_template = $sms->GetOneSmsTemplateByTemplateId($template_id);
			if($sms_template->notes)
			{
				$notesArray=json_decode($sms_template->notes,true);
			}
			else
			{
				$notesArray = null;
			}
			$this->ViewData['note_array'] = $notesArray;
			$this->ViewData['sms_template'] = $sms_template;

			return view('admin.sms_template_edit',$this->ViewData);
		}
	}

	/**
	 * 编辑模板设置
	 * @param Request $request
	 * @return \App\Tools\json
	 */
	public function TemplateEditSubmit(Request $request)
	{
		$m3_result = new M3Result();
		$rules = [
			'merchant_id'=>'required|integer|min:0',
			'template_id'=>'required|integer|min:0',
			'template_name' => 'required|min:2|max:16',
			'template_code' => 'required|min:2|max:16',
			'aliyu_code' => 'required|min:2|max:16',
			'template_content' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if(!$validator->passes())
		{ /*验证失败*/
			$m3_result->code = 1;
			$m3_result->messages = __('admin.failed');
		}
		else
		{
			$sms = new Sms();
			/*新增短信模板*/
			if($request->template_id == 0)
			{
				$result = $sms->SmsTemplateCodeIsUnique($request->template_code);
				if($result == true)
				{
					$smsTemplate = $sms->AddAdminSmsTemplate($request);
					if($smsTemplate == true)
					{
						$m3_result->code = 0;
						$m3_result->messages = __('admin.success');
					}
					else
					{
						$m3_result->code = 2;
						$m3_result->messages = __('admin.failed');
					}
				}
				else
				{
					$m3_result->code = 1;
					$m3_result->messages = '模板编码不允许重复';
				}
			}
			/*编辑短信模板*/
			else
			{
				$smsTemplate = $sms->EditAdminSmsTemplate($request);
				if($smsTemplate == true)
				{
					$m3_result->code = 0;
					$m3_result->messages = __('admin.success');
				}
				else
				{
					$m3_result->code = 2;
					$m3_result->messages = __('admin.failed');
				}
			}

		}
		return $m3_result->toJson();
	}

	public function TemplateDeleteOne(Request $request)
	{
		$m3_result = new M3Result();
		$rules = [
			'template_id'=>'required|integer|min:0',
		];
		$validator = Validator::make($request->all(), $rules);
		if(!$validator->passes())
		{ /*验证失败*/
			$m3_result->code = 1;
			$m3_result->messages = __('admin.failed');
		}
		else
		{
			$sms = new Sms();
			$result = $sms->DeleteOneAdminSmsTemplate($request->template_id);
			if($result == true)
			{
				$m3_result->code = 0;
				$m3_result->messages = __('admin.success');
			}
			else
			{
				$m3_result->code = 2;
				$m3_result->messages = __('admin.failed');
			}
		}
		return $m3_result->toJson();
	}

	/**
	 * 测试发送短信
	 */
	public function TemplateSendSms(Request $request)
	{
		$parameter['code'] = '382649';
		$m3_result = new M3Result();
		$rules = [
			'aliyu_code'=>'required',
			'template_code'=>'required',
			'recNum'=>'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if(!$validator->passes())
		{ /*验证失败*/
			$m3_result->code = 1;
			$m3_result->messages = __('admin.failed');
		}
		else
		{
            /*验证此模板是否可以使用*/
			/*发送模板*/
			$sms = new Sms();
			$smsConfig = $sms->GetOneSmsConfigByMerchantId(0);
            $res = $sms->SendMsm($smsConfig['appkey'],$smsConfig['secretKey'],$smsConfig['signName'],$request->aliyu_code,$request->template_code,$request->recNum,$parameter);
            if($res == true)
            {
	            $m3_result->code = 0;
	            $m3_result->messages = __('admin.success');
            }
			else
			{
				$m3_result->code = 1;
				$m3_result->messages = __('admin.failed');
			}
		}
		return $m3_result->toJson();
	}

}