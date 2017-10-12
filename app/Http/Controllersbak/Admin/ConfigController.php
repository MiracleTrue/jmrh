<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/15 0015
 * Time  : 16:02
 */
namespace App\Http\Controllers\Admin;
use App\Models\Admin;
use App\Models\Menu;
use App\Models\MyFile;
use App\Tools\M3Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;


/**
 * Class 后台 系统设置控制器
 */
class ConfigController extends CommonController
{
    public $ViewData = array(); /*传递页面的数组*/

    /**
     * 后台系统设置 页面
     * @return View
     */
    public function ConfigIndex()
    {
        /*初始化*/
        $admin_model = new Admin();
        $this->ViewData['nav_position'] = Menu::getAdminPosition();
        $this->ViewData['config_list']  = $admin_model->getSystemConfigToPage();

        return view('admin.setting_config_index',$this->ViewData);
    }


    public function ConfigSubmit(Request $request)
    {
        /*初始化*/
        $m3result = new M3Result();
        $admin_model = new Admin();
        $my_file = new MyFile();

        /*验证规则*/
        $rules = [
            'shop_name'     => 'required',
            'shop_closed'   => 'required',
        ];

        if($request->hasFile('shop_logo')){
            $rules['shop_logo'] =  'file|image';
        }
        if($request->hasFile('shop_default_picture')){
            $rules['shop_default_picture'] =  'file|image';
        }
        $validator = Validator::make($request->all(), $rules);


        if($validator->passes())
        {   /*验证通过*/
            $admin_model->setSystemConfig($request);
            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failed');
            $m3result->data    = $validator->messages();
        }

        return $m3result->toJson();

    }
}