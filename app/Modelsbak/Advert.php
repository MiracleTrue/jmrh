<?php

/**
 * Created by laravelShop.
 * Author: maChenChen QQ:482929305
 * Data: 2017/6/29
 * Time: 9:50
 */
namespace App\Models;

use App\Entity\AdvertPosition;
use App\Entity\AdvertEntity;

/**
 * Class Advert  广告有关的模型
 * @package App\Models
 */
class Advert extends CommonModel
{

	/**
	 * 获取全部广告位
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function getAllPosition()
	{
		$ad_position = AdvertPosition::paginate($_COOKIE['AdminPaginationSize']);
		return $ad_position;
	}

	/**
	 * 模糊查询广告位名称
	 * @param $keywords
	 * @return mixed
	 */
	public function AdPositionFuzzyQuery($keywords)
	{
		$ad_position = AdvertPosition::where('position_name','like','%'.$keywords.'%')->paginate($_COOKIE['AdminPaginationSize']);
		return $ad_position;
	}

	/**
	 * 获取一条广告位根据广告ID
	 * @param $position_id
	 * @return mixed
	 */
	public function getOneAdPositionByPositionId($position_id)
	{
		$ad_position = AdvertPosition::find($position_id);
		return $ad_position;
	}

	/**
	 * 新增广告位
	 * @param $adPosition
	 * @return bool
	 */
	public function addAdPosition($adPosition)
	{
		$ad_position = new AdvertPosition();
		$ad_position->position_name = $adPosition['position_name'];
		$ad_position->en_position_name = $adPosition['en_position_name'];
		$ad_position->ad_width      = $adPosition['ad_width'];
		$ad_position->ad_height     = $adPosition['ad_height'];
		$ad_position->position_desc = $adPosition['position_desc'];
		$ad_position->en_position_desc = $adPosition['en_position_desc'];
		$ad_position->status        = $adPosition['status'];
		if($ad_position->save())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 编辑广告位
	 * @param $adPosition
	 * @return bool
	 */
	public function editAdPosition($adPosition)
	{
		$position_id = $adPosition['position_id'];
		$ad_position = AdvertPosition::find($position_id);
		$ad_position->position_name = $adPosition['position_name'];
		$ad_position->en_position_name = $adPosition['en_position_name'];
		$ad_position->ad_width      = $adPosition['ad_width'];
		$ad_position->ad_height     = $adPosition['ad_height'];
		$ad_position->position_desc = $adPosition['position_desc'];
		$ad_position->en_position_desc = $adPosition['en_position_desc'];
		$ad_position->status        = $adPosition['status'];
		if($ad_position->save())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 删除一条广告位
	 * @param $position_id
	 * @return bool
	 */
	public function deleteOneAdPosition($position_id)
	{
		/*查看此广告位下是否有广告*/

		$ad_position = AdvertPosition::find($position_id);
		if($ad_position->delete())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 判断广告位下是否存在广告
	 * @param $position_id
	 * @return bool
	 */
	public function AdPositionIsExistAd($position_id)
	{
		$ad  = AdvertEntity::where('position_id',$position_id)->get();
		$count = count($ad);
		if($count == 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * 批量删除广告位
	 * @param $positionIds
	 * @return bool
	 */
	public function deleteAdPosition($positionIds)
	{
		if(AdvertPosition::destroy($positionIds))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 验证广告位名称的唯一性
	 * @param $position_name
	 * @return bool
	 */
	public function erifyuUniquePosition($position_name)
	{
		$ad_position = AdvertPosition::where('position_name',$position_name)->get();
        if(count($ad_position) == 0)
        {
	        return true;
        }
		else
		{
			return false;
		}
	}

	/**
	 * 获取广告列表通过广告位id
	 * @param $position_id
	 * @return mixed
	 */
	public function getAdvertListByPositionId($position_id)
	{
		$adverts = AdvertEntity::where('position_id',$position_id)->get();
		return $adverts;
	}

	/**
	 * 获取一条广告通过广告id
	 * @param $ad_id
	 * @return mixed
	 */
	public function getOneAdvertByAdvertId($ad_id)
	{
		$advert = AdvertEntity::find($ad_id);
		return $advert;
	}

	/**
	 * 新增广告
	 * @param $advert
	 * @return bool
	 */
	public function addAdvert($advert)
	{
		$ad_entity = new AdvertEntity();
		$ad_entity->position_id = $advert['position_id'];
		$ad_entity->ad_link = $advert['ad_link'];
		$ad_entity->start_time = $advert['start_time'];
		$ad_entity->end_time = $advert['end_time'];
		$ad_entity->ad_path = $advert['ad_path'];
        if($ad_entity->save())
        {
	        return true;
        }
		else
		{
			return false;
		}
	}

	/**
	 * 编辑广告
	 * @param $advert
	 * @return bool
	 */
	public function editAdvert($advert)
	{
		$ad_entity = AdvertEntity::find($advert['ad_id']);
		$ad_entity->position_id = $advert['position_id'];
		$ad_entity->ad_link = $advert['ad_link'];
		$ad_entity->start_time = $advert['start_time'];
		$ad_entity->end_time = $advert['end_time'];
		$ad_entity->ad_path = $advert['ad_path'];
		if($ad_entity->save())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 删除一条广告
	 * @param $ad_id
	 * @return bool
	 */
	public function deleteOneAdvert($ad_id)
	{
		$advert = AdvertEntity::find($ad_id);
		if($advert->delete())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

}