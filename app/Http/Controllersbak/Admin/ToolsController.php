<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/15 0015
 * Time  : 16:02
 */

namespace App\Http\Controllers\Admin;
use App\Models\MyFile;
use App\Tools\M3Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class 后台 工具类控制器
 */
class ToolsController extends CommonController
{
    public $ViewData = array(); /*传递页面的数组*/


    /**
     * 上传单张临时展示的图片,方便展示(返回http可访问的图片全路径)
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ImagePreview(Request $request)
    {
        /*初始化*/
        $m3result = new M3Result();
        $my_file  = new MyFile();

        /*验证规则*/
        $rules['image'] =  'required|file|image';
        $validator = Validator::make($request->all(), $rules);

        if($validator->passes())
        {   /*验证通过*/
            $path = $my_file->uploadTemp($request->file('image'));
            $m3result->code    = 0;
            $m3result->messages= '上传成功！';
            $m3result->data    = $path;
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= '上传失败！';
            $m3result->data    = $validator->messages();
        }

        return $m3result->toJson();
    }

    /**
     * 上传单张永久保存的图片,永久保存(返回http可访问的图片全路径 与 可存储至数据库的图片路径)
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ImageSave(Request $request)
    {
        /*初始化*/
        $m3result = new M3Result();
        $my_file  = new MyFile();

        /*验证规则*/
        $rules = array(
            'image' => 'required|file|image|max:500',/*限制500KB以内的文件*/
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->passes())
        {   /*验证通过*/
            $path = $my_file->uploadOriginal($request->file('image'));
            $m3result->code    = 0;
            $m3result->messages= '上传成功！';
            $m3result->data['url']     = $my_file->makeUrl($path);
            $m3result->data['base']    = $path;
        }
        else
        {
            $m3result->data    = $validator->messages();
            $m3result->code    = 1;
            $m3result->messages= '上传失败！';

            if(in_array('validation.max.file',$m3result->data->all()))
            {
                $m3result->code    = 2;
                $m3result->messages= '上传超过大小限制！';
            }

        }
        return $m3result->toJson();
    }

    /**
     * 上传属性小图,永久保存(返回http可访问的图片全路径 与 可存储至数据库的图片路径)
     * @param Request $request
     * @return \App\Tools\json
     */
    public function ImageAttrSave(Request $request)
    {
        /*初始化*/
        $m3result = new M3Result();
        $my_file  = new MyFile();

        /*验证规则*/
        $rules = array(
            'image' => 'required|file|image|max:100',/*限制100KB以内的文件*/
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->passes())
        {   /*验证通过*/
            $path = $my_file->uploadAttr($request->file('image'));
            $m3result->code    = 0;
            $m3result->messages= '上传成功！';
            $m3result->data['url']     = $my_file->makeUrl($path);
            $m3result->data['base']    = $path;
        }
        else
        {
            $m3result->data    = $validator->messages();
            $m3result->code    = 1;
            $m3result->messages= '上传失败！';

            if(in_array('validation.max.file',$m3result->data->all()))
            {
                $m3result->code    = 2;
                $m3result->messages= '上传超过大小限制！';
            }

        }
        return $m3result->toJson();
    }
}