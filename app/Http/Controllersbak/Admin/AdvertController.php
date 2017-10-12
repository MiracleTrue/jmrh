<?php
/**
 * Created by LaravelShop.
 * Author: maChenChen  QQ:482929305
 * Date  : 2017/5/15 0015
 * Time  : 16:02
 */
namespace App\Http\Controllers\Admin;

use App\Entity\AdvertPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Tools\M3Result;
use App\Models\Advert;
use App\Models\MyFile;
use App\Models\CommonModel;
use App\Models\Menu;
use Illuminate\Validation\Rule;

/**
 * Class 后台 广告控制类
 */
class AdvertController extends CommonController
{
	/**
	 * 广告位置显示
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function PositionIndex()
	{
		$ad = new Advert();
		$common = new CommonModel();
		$ad_position = $ad->getAllPosition();
		foreach($ad_position as $item)
		{
			$item['position_name'] = $common->languageFormat($item->position_name,$item->en_position_name);
			$item['position_desc'] = $common->languageFormat($item->position_desc,$item->en_position_desc);
		}
		$count = count($ad_position);
		$this->ViewData['picture_position'] = $ad_position;
		$this->ViewData['count'] = $count;
		$this->ViewData['nav_position'] = Menu::getAdminPosition();
		return view('admin.ad_position_index',$this->ViewData);
	}

	/**
	 * 广告位-广告名称搜素
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function PositionFuzzyQuery(Request $request)
	{
		$ad = new Advert();
		$common = new CommonModel();
		$ad_position = $ad->AdPositionFuzzyQuery($request->position_name);
		foreach($ad_position as $item)
		{
			$item['position_name'] = $common->languageFormat($item->position_name,$item->en_position_name);
			$item['position_desc'] = $common->languageFormat($item->position_desc,$item->en_position_desc);
		}
		$count = count($ad_position);
		$this->ViewData['picture_position'] = $ad_position;
		$this->ViewData['count'] = $count;
		$this->ViewData['nav_position'] = Menu::getAdminPosition();
		return view('admin.ad_position_index',$this->ViewData);

	}
	/**
	 * 新增广告位页面
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function PositionView($position_id = 0)
	{
		$ad = new Advert();
		$adPosition = null;
		if($position_id != 0)
		{
			$adPosition = $ad->getOneAdPositionByPositionId($position_id);
		}
		return view('admin.ad_position_edit',['picturePosition'=>$adPosition]);
	}
	/**
	 * 新增或编辑广告位
	 * @param Request $request
	 * @return \App\Tools\json
	 */
	public function PositionSubmit(Request $request)
	{
		$m3_result = new M3Result();
		$ad = new Advert();
		/* 验证数据*/
		$rules =
		[
			'position_id' => 'required|integer|min:0',
			'position_name' => 'required|min:4|max:20',
			'ad_width'       => 'required|integer|min:0',
			'ad_height'  => 'required|integer|min:0',
			'status' => 'required|integer',
		];
		$validator = Validator::make($request->all(), $rules);
		if($validator->fails())
		{
			$m3_result->code = 1;
			$m3_result->messages = __('admin.failed');
		}
		else
		{
			if(!isset($request->en_position_name))
			{
				$request['en_position_name'] = '';
			}
			if(!isset($request->position_desc))
			{
				$request['position_desc'] = '';
			}
			if(!isset($request->en_position_desc))
			{
				$request['en_position_desc'] = '';
			}
			/*新增广告位*/
			if($request->position_id == 0)
			{
				/*验证广告位名称是否重复; */
				$erifyuUniquePosition = $ad->erifyuUniquePosition($request->position_name);
				if($erifyuUniquePosition == false)
				{
					$m3_result->code = 1;
					$m3_result->messages = '广告位名称不能重复';
				}
				else{
					$adPosition = $ad->addAdPosition($request);
					if($adPosition == true)
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
			}
			/*编辑广告位 */
			else
			{
				$adPosition = $ad->editAdPosition($request);
				if($adPosition == true)
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
		}
		return $m3_result->toJson();
	}

	/**
	 * 删除广告位
	 * @param Request $request
	 * @return \App\Tools\json
	 */
	public function PositionDelOne(Request $request)
	{
		$m3_result = new M3Result();
		$ad = new Advert();
		/* 验证数据*/
		$rules =
			[
				'position_id' => 'required|integer|min:0',
			];
		$validator = Validator::make($request->all(), $rules);
		if($validator->fails())
		{
			$m3_result->code = 1;
			$m3_result->messages = __('admin.failed');
		}
		else
		{
			$advert = $ad->AdPositionIsExistAd($request->position_id);
			if($advert == true)
			{
				$adPosition = $ad->deleteOneAdPosition($request->position_id);
				if($adPosition == true)
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
			else
			{
				$m3_result->code = 1;
				$m3_result->messages = __('admin.picture.exist_ad');
			}
		}
		return $m3_result->toJson();
	}

	/**
	 * 批量删除广告位
	 * @param Request $request
	 * @return \App\Tools\json
	 */
	public function PositionBatchDelete(Request $request)
	{
		$ad = new Advert();
		$m3_result = new M3Result();
		$chk_position_id = $request->chk_position_id;
		$isDel = $ad->deleteAdPosition($chk_position_id);
		if($isDel == true)
		{
			$m3_result->code = 0;
			$m3_result->messages = __('admin.success');
		}
		else
		{
			$m3_result->code = 1;
			$m3_result->messages = __('admin.failed');
		}
		return $m3_result->toJson();
	}

	/**
	 * 快捷操作广告位
	 * @param Request $request
	 * @return \App\Tools\json
	 */
	public function PositionQuickEdit(Request $request)
	{
		/*初始化*/
		$adPosition = new AdvertPosition();
		$m3_result = new M3Result();

		/*验证*/
		$rules = [
			'position_id'     => [
				'required',
				'integer',
				Rule::exists('advert_position')->where(function ($query) {
					$query->where('position_id',$GLOBALS['request']->input('position_id'));
				}),
			],
			'merchant_id'     => [
				'sometimes',
				'required',
				'integer',
				Rule::exists('advert_position')->where(function ($query) {
					$query->where('merchant_id',$GLOBALS['request']->input('merchant_id'));
				}),
			],
			'position_name'   => [
				'sometimes',
				'required',
				'between:4,16',
				Rule::unique('advert_position')->ignore($request->input('position_id') , 'position_id'),
			],
			'en_position_name'=> 'sometimes|required|min:4|max:16',
			'ad_width'        => 'sometimes|required|integer',
			'ad_height'       => 'sometimes|required|integer',
			'position_desc'   => 'sometimes|required',
			'en_position_desc'=> 'sometimes|required',
			'en_position_desc'=> 'sometimes|required|integer',
			'status' => 'sometimes|required|integer',
		];
		$validator = Validator::make($request->all(), $rules);

		if($validator->fails())
		{
			$m3_result->code = 1;
			$m3_result->messages = __('admin.failed');
		}
		else
		{
			/*过滤更新字段,只更新在$rules验证字段存在时的字段*/
			list($rules_keys) = array_divide($rules);
			$adPosition->where('position_id', $request->input('position_id'))->update(array_only($request->all(), $rules_keys));

			$m3_result->code    = 0;
			$m3_result->data = $request->position_id;
			$m3_result->messages= __('admin.success');
		}
		return $m3_result->toJson();
	}


	/**
	 * 广告新增或编辑页
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function EntityView($position_id)
	{
		$ad = new Advert();
		$adverts = $ad->getAdvertListByPositionId($position_id);
		return view('admin.ad_entity_edit',['position'=>$adverts,'position_id'=>$position_id]);
	}

	/**
	 * 新增或编辑广告
	 * @param Request $request
	 * @return \App\Tools\json
	 */
	public function EntitySubmit(Request $request)
	{
		$m3_result = new M3Result();
		$ad = new Advert();
		/* 验证数据*/
		$rules =
			[
				'ad_id'=>'required|integer|min:0',
				'position_id'=>'required|integer|min:0',
				'start_time' => 'required|date',
				'end_time' => 'required|date',
			];
		if($request->hasFile('advert_file'))
		{
			$rules['advert_file'] =  'file|image';
		}
		$validator = Validator::make($request->all(), $rules);
		if($validator->fails())
		{
			$m3_result->code = 1;
			$m3_result->messages = "数据验证失败";
		}
		else
		{
			if(!isset($request->ad_link))
			{
				$request['ad_link'] = '';
			}
			$request['start_time'] = strtotime($request['start_time']);
			$request['end_time'] = strtotime($request['end_time']);
			/* 新增广告（通过） */
			if($request->ad_id == 0){
				if($request->hasFile('advert_file'))
				{
					$my_file = new MyFile();
					$path = $my_file->uploadOriginal($request->file('advert_file'));/*上传成功*/
					$request['ad_path'] = $path;
					/*新增广告 */
					$advert = $ad->addAdvert($request);
					if($advert == true)
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
				else
				{
					$m3_result->code = 1;
					$m3_result->messages = __('admin.failed');
				}
			}
			else
			{
				if($request->hasFile('advert_file')) {
					$my_file = new MyFile();
					$path = $my_file->uploadOriginal($request->file('advert_file'));/*上传成功*/
					$request['ad_path'] = $path;
				}
				else
				{
					$advert = $ad->getOneAdvertByAdvertId($request->ad_id);
					$request['ad_path'] = $advert->ad_path;
				}
				$advert = $ad->editAdvert($request);
				if($advert == true)
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
		}
		return $m3_result->toJson();
	}

	/**
	 * 删除一条广告
	 * @param Request $request
	 * @return \App\Tools\json
	 */
	public function EntityDelOne(Request $request)
	{
		$m3_result = new M3Result();
		$ad = new Advert();
		/* 验证数据*/
		$rules =
			[
				'ad_id' => 'required|integer|min:0',
			];
		$validator = Validator::make($request->all(), $rules);
		if($validator->fails())
		{
			$m3_result->code = 1;
			$m3_result->messages = __('admin.failed');
		}
		else
		{
			$advert = $ad->deleteOneAdvert($request->ad_id);
			if($advert == true)
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

	/*
	 *广告位显示测试
	 */
	public function AdvertDisplayTest()
	{
		return view('web.ad_display_test');
	}
}