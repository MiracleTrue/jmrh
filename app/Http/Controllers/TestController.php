<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public $ViewData = array(); /*传递页面的数组*/

    public function Index(Request $request)
    {
        /*初始化*/

        User::userLog();

        return 'test';
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