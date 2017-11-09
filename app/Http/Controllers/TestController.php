<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */
namespace App\Http\Controllers;

use App\Entity\Orders;
use App\Models\CommonModel;
use App\Models\User;
use App\Tools\MyHelper;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public $ViewData = array(); /*传递页面的数组*/

    public function Index(Request $request)
    {
        $my_helper = new MyHelper();
        var_dump(
            $my_helper->is_timestamp(Orders::find(83)->army_receive_time) ? date('YmdHis',Orders::find(83)->army_receive_time) : 'now'
        );

        var_dump(
            date('YmdHis',Orders::find(83)->army_receive_time)
        );



        dd(date('YmdHis',1510135244));
        /*初始化*/
//        $a = new CommonModel();
//
//        $a->autoTest();
//
//        return 'test';
    }

    public function T_add(Request $request)
    {
        /**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**/

        $arr = array(
            'identity' => '1',
            'user_name'=>'A-'.now(),
            'nick_name'=>'N-'.now(),
            'password'=>'123456',
            'phone'=>'18600982820',
        );
        $request->merge($arr);

        /**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**/

        $arr = array(
            'product_name' => '蔬菜'.mt_rand(1,999),
            'product_number' => mt_rand(8, 888),
            'product_unit' => '个',
            'confirm_time' => '2017-11-1',
            'platform_receive_time' => '2017-11-6',
            'supplier_B' => '40',
            'supplier_A' => '42',
            'supplier_C' => '41',
        );
        $request->merge($arr);

        /**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**/

        $arr = array(
            'order_id' => '1',
        );
        $request->merge($arr);

        /**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**/


//        dd($request->all());

        /*初始化*/
        $a = new User();

        $a->addUser($request);


        return 'test';
    }

    public function T_list()
    {
        /*初始化*/

        return 'test';
    }

    public function T_update()
    {
        /*初始化*/

        return 'test';
    }

    public function T_delete()
    {
        /*初始化*/

        return 'test';
    }

}