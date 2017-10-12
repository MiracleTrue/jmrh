<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/18 0018
 * Time  : 14:17
 */

namespace App\Models;
use Illuminate\Support\Facades\Request;

/**
 * Class CommonModel 基础模型
 * @package App\Models
 */
class CommonModel {

    const SELF_EMPLOYED_ID = 0;/*merchant自营id*/
//    DB::enableQueryLog();//开启查询
//
//    dd(DB::getQueryLog());//打印查询SQL

//
//        $route = Route::current();
//
//        $name = Route::currentRouteName();
//
//        $action = Route::currentRouteAction();
//
//        dump($route);
//        dump($name);
//        dd($action);

    /**
     * 根据参数位置的中英文转换函数,用于数据库取出的动态数据(如没有英文名,优先显示中文)
     * @param string $zh_data 中文
     * @param string $en_data 英文
     * @return string
     */
    public static function languageFormat($zh_data = '' , $en_data = '')
    {
        $locale_language = Request::session()->get('AdminLanguage');

        if($locale_language == 'en')
        {
            if(!empty($en_data))
            {
                return $en_data;
            }
            else
            {
                return $zh_data;
            }

        }
        else
        {
            return $zh_data;
        }
    }


    /**
     * 函数返回不包含命名空间的类名称：可用与action()生成url
     * @param $str
     * @return string
     */
    public static function controllerStringConversion($str)
    {
        return 'Admin\\'.class_basename($str);
    }
    
    /**
     * 修改.env 文件配置
     * @param array $data
     */
    public function modifyEnv(array $data)
    {
        $envPath = base_path() . DIRECTORY_SEPARATOR . '.env';

        $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));

        $contentArray->transform(function ($item) use ($data){
            foreach ($data as $key => $value){
                if(str_contains($item, $key)){
                    return $key . '=' . $value;
                }
            }

            return $item;
        });

        $content = implode($contentArray->toArray(), "\n");

        \File::put($envPath, $content);
    }
}