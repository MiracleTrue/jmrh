<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */
namespace App\Http\Controllers;

use App\Entity\OrderOffer;
use App\Entity\Orders;
use App\Models\CommonModel;
use App\Models\Sms;
use App\Models\User;
use App\Tools\MyHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TestController extends Controller
{
    public $ViewData = array(); /*传递页面的数组*/

    public function Index(Request $request)
    {

        dd($_COOKIE);
        $my_helper = new MyHelper();
        var_dump(
            $my_helper->is_timestamp(Orders::find(83)->army_receive_time) ? date('YmdHis', Orders::find(83)->army_receive_time) : 'now'
        );

        var_dump(
            date('YmdHis', Orders::find(83)->army_receive_time)
        );


        dd(date('YmdHis', 1510135244));
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
            'user_name' => 'A-' . now(),
            'nick_name' => 'N-' . now(),
            'password' => '123456',
            'phone' => '18600982820',
        );
        $request->merge($arr);

        /**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**/

        $arr = array(
            'product_name' => '蔬菜' . mt_rand(1, 999),
            'product_number' => mt_rand(8, 888),
            'product_unit' => '个',
            'confirm_time' => '2017-12-1',
            'platform_receive_time' => '2017-12-6',
            'supplier_B' => '40',
            'supplier_A' => '41',
        );
        $request->merge($arr);


        /**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**/

        $arr = array(
            'order_id' => '1',
        );
        $request->merge($arr);

        /**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**/

        return 'test';
    }

    public function T_list()
    {
        /*初始化*/
        $now_time = now()->timestamp;
        static $o_id = 0;
        //查询出所有 "待报价" 的offer 并且已经再过期时间的
        $e_order_offer = OrderOffer::where('status', CommonModel::OFFER_AWAIT_OFFER)->where('confirm_time', '<', $now_time)->get();

        dd($now_time, $e_order_offer);

        return 'test';
    }

    public function T_update()
    {

        return 'test';
    }

    public function T_delete()
    {
        /*初始化*/
//        $sms = new Sms();
//
//        $response = $sms->sendSms(
//            Sms::SMS_SIGNATURE_1, // 短信签名
//            Sms::SELECT_SUPPLIER_CODE, // 短信模板编号
//            "18600982820" // 短信接收者
//        );
//        if($response->Code != 'OK'){
//            print_r($response->Message);
//        }

        OrderOffer::where('order_id', 108)->delete();
        Orders::where('order_id', 108)->update(['status' => 1]);


        return 'test';
    }

    public function T_table()
    {
        $cellData = [
            ['学号', '姓名', '成绩'],
            ['10001', 'AAAAA', '99'],
            ['10002', 'BBBBB', '92'],
            ['10003', 'CCCCC', '95553'],
            ['10004', 'DDDDD', '89321'],
            ['10005', 'EEEEE', '96'],
        ];
        Excel::create('学生成绩', function ($excel) use ($cellData)
        {
            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $excel->sheet('score', function ($sheet) use ($cellData)
            {
                $sheet->setStyle(array(
                    'width' => '500',
                    'font' => array(
                        'name' => 'Calibri',
                        'size' => 18,
                        'bold' => true,
                        'text-align' => 'center'
                    )
                ));
                $sheet->rows($cellData);
                $sheet->cells('A1:C1', function ($cells)
                {

                    // manipulate the range of cells
                    $cells->setBackground('#ff2832');
                    $cells->setFont(array(
                        'size' => '16',
                        'bold' => true
                    ));
                });
            });
        })->export('xls');


        /*改变所有行*/
//        Excel::create('Users Report', function ($excel) use ($arrUsers)
//        {
//
//
//            $excel->sheet('Users', function ($sheet) use ($arrUsers)
//            {
//
//                // Set all margins
//                $sheet->fromArray($arrUsers, null, 'A1', true);
//
//                for ($intRowNumber = 1; $intRowNumber <= count($arrUsers) + 1; $intRowNumber++)
//                {
//                    $sheet->setSize('A' . $intRowNumber, 25, 18);
//                    $sheet->setSize('B' . $intRowNumber, 25, 18);
//                    $sheet->setSize('C' . $intRowNumber, 25, 18);
//                    $sheet->setSize('D' . $intRowNumber, 25, 18);
//                    $sheet->setSize('E' . $intRowNumber, 25, 18);
//                    $sheet->setSize('F' . $intRowNumber, 25, 18);
//                }
//
//                $sheet->row(1, array(
//                    'Name', 'Username', 'Contact', 'Email', 'Verified', 'Inactivity'
//                ));
//
//                // Freeze first row
//                $sheet->freezeFirstRow();
//
//                $sheet->cell('A1:F1', function ($cell)
//                {
//
//                    // Set font
//                    $cell->setFont(array(
//                        'family' => 'Calibri',
//                        'size' => '12',
//                        'bold' => true
//                    ));
//
//                });
//
//            });
//        })->store('xls')->download('xls');

        return 'table';
    }

}