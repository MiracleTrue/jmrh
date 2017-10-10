<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 *
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {

//        DB::enableQueryLog();//开启查询
//
//    dd(DB::getQueryLog());//打印查询SQL
//        $admin_model = new Admin();

        /*全局config配置,并共享所有视图*/
//        $GLOBALS['shop_config'] = $admin_model->getSystemConfig();
//        View::share('shop_config',$GLOBALS['shop_config']);
//
//        /*初始化分页大小 15条*/
//        if(empty($_COOKIE['AdminPaginationSize']) || is_numeric($_COOKIE['AdminPaginationSize']) == false) {$_COOKIE['AdminPaginationSize'] = 15;}
    }
}
