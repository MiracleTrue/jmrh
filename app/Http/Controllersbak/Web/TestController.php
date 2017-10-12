<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/15 0015
 * Time  : 16:02
 */

namespace App\Http\Controllers\Web;


use Intervention\Image\Facades\Image;

class TestController extends CommonController
{

    /**
     * 测试程序中修改.env文件
     * @return string
     */
//    public function Index()
//    {
//        $arr = array(
//            'APP_ENV' => 'your_environment',
//            'APP_NAME'=> 'your_name',
//        );
//        $base_model = new CommonModel();
//        $base_model->modifyEnv($arr);
//
//
//        return 'test';
//    }


/*后台系统设置选择范围的json*/
//$arr =array();
//$arr[0]['value'] = '1';
//$arr[0]['name'] = '是';
//$arr[0]['en_name'] = 'Yes';
//$arr[1]['value'] = '0';
//$arr[1]['name'] = '否';
//$arr[1]['en_name'] = 'No';

    public function Img()
    {

        $img = Image::canvas(800, 600, '#ff0330');

        // send HTTP header and output image data
        header('Content-Type: image/png');
        echo $img->encode('png');
        exit();
    }

    public function Index()
    {
        $arr =array();
        $arr[0]['value'] = '1';
        $arr[0]['name'] = '是';
        $arr[0]['en_name'] = 'Yes';
        $arr[1]['value'] = '0';
        $arr[1]['name'] = '否';
        $arr[1]['en_name'] = 'No';
        return $arr;
    }
}