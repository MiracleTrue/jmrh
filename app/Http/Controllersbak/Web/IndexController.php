<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/15 0015
 * Time  : 16:02
 */

namespace App\Http\Controllers\Web;

/**
 * Class PC站 首页控制器
 */
class IndexController extends CommonController
{

    public function Index()
    {
        return view('web.index');
    }
}