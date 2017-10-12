<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/15 0015
 * Time  : 16:02
 */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Rbac;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

/**
 * Class 后台 基类控制器
 */
class CommonController extends Controller
{
    public function __construct()
    {
        DB::enableQueryLog();//开启查询
//
//    dd(DB::getQueryLog());//打印查询SQL
        $admin_model = new Admin();

        /*全局config配置,并共享所有视图*/
        $GLOBALS['shop_config'] = $admin_model->getSystemConfig();
        View::share('shop_config',$GLOBALS['shop_config']);

        /*初始化分页大小 15条*/
        if(empty($_COOKIE['AdminPaginationSize']) || is_numeric($_COOKIE['AdminPaginationSize']) == false) {$_COOKIE['AdminPaginationSize'] = 15;}
    }
}
