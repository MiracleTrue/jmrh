<?php
/**
 * Created by LaravelShop.
 * Author: maChenChen  QQ:482929305
 * Date  : 2017/5/15 0015
 * Time  : 16:02
 */
namespace App\Http\Controllers\Admin;

use App\Tools\M3Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Entity\MailTemplate;
use App\Models\Email;
use App\Models\Menu;


/**
 * Class 后台 邮件控制类
 */
class EmailController extends CommonController
{
	public $ViewData = array(); /*传递页面的数组*/
	/**
	 * 邮件菜单栏目列表页面
	 * @return View
	 */
	public function ServerView()
	{
		$email = new Email();
		$this->ViewData['emailConfig'] = $email->GetOneEmailConfigByMerchantId();
		$this->ViewData['nav_position'] = Menu::getAdminPosition();
		return view('admin.email_server_edit',$this->ViewData);
	}
	/**
	 * 邮件服务器设置；
	 * @return Json
	 */
	public function ServerEditSubmit(Request $request)
	{
		$m3_result = new M3Result();
		$email = new Email();
		$data = $request->all();
		$Config_id = $data['emailConfig_id'];
		$identity = $data['identity'];
		//验证数据
		$rules = [
			'smtpServer' => 'required|',
			'port'       => 'required|integer|min:0',
			'emailFrom'  => 'required|email',
			'emailPossword' => 'required',
		];
		$validator = Validator::make($data, $rules);
		if(!$validator->passes())
		{ /*验证失败*/
			$m3_result->code = 1;
			$m3_result->messages = __('admin.failed');
		}
		else
		{
			/* 如果是自营商家 */
			if($identity == 0)
			{
				if($Config_id)
				{
					$result = $email->EditAdminEmailServer($request);
				}
				else
				{
					$result = $email->AddAdminEmailServer($request);
				}
			}
			else  /*如果是入驻商家*/
			{
				/*identity 应该填写入驻商家的id */
				$request['identity'] = 1;
				$result = $email->AddAdminEmailServer($request);
			}
			if($result)
			{
				$m3_result->code = 0;
				$m3_result->messages = __('admin.success');
			}else
			{
				$m3_result->code = 2;
				$m3_result->messages = __('admin.failed');
			}
		}

		return $m3_result->toJson();

	}
	/**
	 * 邮件模板页;
	 * return view;
	 */
	public function TemplateView()
	{
		$email = new Email();
		$this->ViewData['mail_template'] = $email->GetListTemplate();
		$this->ViewData['nav_position'] = Menu::getAdminPosition();
		return view('admin.email_template_edit',$this->ViewData);
	}
	/**
	 * 获取一条邮件模板;
	 * return view;
	 */
	public function GetOneTemplateById($template_id)
	{
		$email = new Email();
		$mail_template = $email->GetListTemplate();
		$one_template = $email->GetOneTemplateByTemplateId($template_id);
		$notesJson = $one_template->notes;
		$note_array = null;
		if($notesJson)
		{
			$note_array=json_decode($notesJson,true);
		}
		return view('admin.email_template_edit',['mail_template' => $mail_template,'one_template'=>$one_template,'note_array'=>$note_array]);
	}
    /**
     * 保存邮件模板设置;
     * @request 模板参数
     * return Json;
     */
	public function TemplateEditSubmit(Request $request)
	{
		$email = new Email();
		$m3_result = new M3Result();
		//验证数据
		$rules = [
			'merchant_id' => 'required|integer|min:0',
			'template_code' => 'required',
			'subject'     => 'required|min:2|max:16',
			'content'     => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if(!$validator->passes())
		{ /*验证失败*/
			$m3_result->code = 1;
			$m3_result->messages = __('admin.failed');
		}
		else
		{
			$result = $email->EditAdminEmailTemplate($request);
			if($result)
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


	/**
	 * 调用发送邮件功能测试方法
	 * @param Request $request
	 * @return \App\Tools\json
	 */
	public function SendTest(Request $request)
	{
		$m3_result = new M3Result();
		$email = new Email();
		$rules = [
			'merchant_id'   => 'required|integer|min:0',
			'template_code'   => 'required',
			'emailTo'       => 'required|email',
		];
		$validator = Validator::make($request->all(), $rules);
		if(!$validator->passes())
		{
			$m3_result->code = 1;
			$m3_result->messages = '验证失败';
		}
		$merchant_id   = $request->merchant_id;
		$template_code = $request->template_code;
		$emailTo       = $request->emailTo;
		$contentData['user_name']   = "Mark";
		$contentData['reset_email']   = "点击链接";
		$result = $email->EmailSend($merchant_id,$template_code,$emailTo,$contentData);
		if($result){
			$m3_result->code = 0;
			$m3_result->messages = '发送成功';
		}
		else
		{
			$m3_result->code = 2;
			$m3_result->messages = '发送失败';
		}
		return $m3_result->toJson();
	}

/*     email 验证 转json
         $group_buy = array(
			'consignee'=>array(
				'zh_desc'    => '收货人',
				'en_desc'    => 'Consignee',
				'is_required'=> '0'
			),
			'order_time'=>array(
				'zh_desc'    => '用户下单时间',
				'en_desc'    => 'User order time',
				'is_required'=> '0'
			),
			'goods_name'=>array(
				'zh_desc'    => '团购商品名称',
				'en_desc'    => 'Commodity name',
				'is_required'=> '0'
			),
			'goods_number'=>array(
				'zh_desc'    => '团购数量',
				'en_desc'    => 'Group buying quantity',
				'is_required'=> '0'
			),
			'order_sn'=>array(
				'zh_desc'    => '订单号',
				'en_desc'    => 'Order number',
				'is_required'=> '0'
			),
			'order_amount'=>array(
				'zh_desc'    => '订单金额',
				'en_desc'    => 'Order money',
				'is_required'=> '0'
			),
			'shop_url'=>array(
				'zh_desc'    => '订单付款链接',
				'en_desc'    => 'Order payment link',
				'is_required'=> '0'
			),
			'shop_name'=>array(
				'zh_desc'    => '商店名称',
				'en_desc'    => 'Shop name',
				'is_required'=> '1'
			),
			'send_date'=>array(
				'zh_desc'    => '发送日期',
				'en_desc'    => 'Date of dispatch',
				'is_required'=> '1'
			),
			'validate'=>array(
				'zh_desc'    => '数据验证',
				'en_desc'    => 'Data validation',
				'is_required'=> '0',
				'content'    => [
					'consignee'  => 'required',
					'order_time' => 'required|date',
					'goods_name' => 'required',
					'goods_number' => 'required|integer|min:0r',
					'order_sn'   => 'required',
					'order_amount'  => 'required',
					'shop_url' => 'required',
					'send_date' => 'date',
				]
			)
		);
    $json = json_encode($group_buy);
	$file  = 'C:/Users/Administrator/Desktop/xiangmu/emial_template_json.txt';//要写入文件的文件名（可以是任意文件名），如果文件不存在，将会创建一个
    if($f  = file_put_contents($file, $json,FILE_APPEND))
    {// 这个函数支持版本(PHP 5)
	 echo "success";
    }

 */

}