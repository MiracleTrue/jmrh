<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */
namespace App\Http\Controllers;

use App\Entity\Users;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

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
        /*初始化分页大小 10条*/
        if(empty($_COOKIE['PaginationSize']) || is_numeric($_COOKIE['PaginationSize']) == false) {$_COOKIE['PaginationSize'] = 10;}

        /*加入session*/
        session(['ManageUser' => Users::find(1)]);


//        army  军方    platform  平台    supplier   供货商

//        身份标识: 1.超级管理员  2.平台运营员 3.供货商  4.军方  0.无效
//        报价状态:  -1.已超期   0.待报价   1.等待通过    2.未通过   3.已通过   4.已发货

//        订单状态:
//        0.待分配   1.重新分配
//        100.已分配供应商    110.已选择供应商    120.供应商已发货   130.供应商已到货
//        200.库存供应
//        1000.已发货到军方
//        9000.军方已收货(交易成功)


        DB::enableQueryLog();//开启查询
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
