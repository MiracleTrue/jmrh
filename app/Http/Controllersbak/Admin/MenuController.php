<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/15 0015
 * Time  : 16:02
 */
namespace App\Http\Controllers\Admin;

use App\Entity\SystemMenus;
use App\Models\Menu;
use App\Tools\M3Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class 后台 栏目管理控制器
 */
class MenuController extends CommonController
{
    public $ViewData = array(); /*传递页面的数组*/


    /**
     * 系统菜单栏目列表页面
     * @return View
     */
    public function MenusIndex()
    {
        $menu = new Menu();
        $this->ViewData['nav_position'] = Menu::getAdminPosition();
        $this->ViewData['list'] = $menu->getAdminMenus();
        $this->ViewData['count']= SystemMenus::count();
        return view('admin.setting_menus_index',$this->ViewData);
    }

    /**
     * 编辑栏目页面
     * @param int $menu_id
     * @return View
     */
    public function MenusView($menu_id = 0)
    {
        /*获取一级分类*/
        $this->ViewData['menu_parent'] = SystemMenus::where('parent_id',0)->get();
        $this->ViewData['menu_info']   = null;
        /*判断ID决定新增还是修改*/
        if($menu_id > 0)
        {   /*编辑数据*/
            $this->ViewData['menu_info']  = SystemMenus::find($menu_id);
        }
        $this->ViewData['form_url'] = action('Admin\MenuController@MenusEditSubmit');
        return view('admin.setting_menus_edit',$this->ViewData);
    }

    /**
     * 获取指定栏目信息
     * @param Request $request
     * @return \App\Tools\json
     */
    public function MenusGetOne(Request $request)
    {
        /*初始化*/
        $m3result = new M3Result();

        if($request->input('menu_id') > 0)
        {
            $m3result->code    = 0;
            $m3result->messages= __('admin.successData');
            $m3result->data    = SystemMenus::find($request->input('menu_id'));
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failedData');
        }
        return $m3result->toJson();

    }
    /**
     * Ajax新增与编辑栏目提交
     * @param Request $request
     * @return \App\Tools\json
     */
    public function MenusEditSubmit(Request $request)
    {
        /*初始化*/
        $m3result = new M3Result();
        $menu     = new Menu();

        if($request->input('menu_id') == 0)/*新增栏目*/
        {
            if($request->input('parent_id') == 0)/*是否为一级栏目*/
            {
                /*验证规则*/
                $rules = [
                    'menu_id'     => 'required|unique:system_menus,menu_id|integer',
                    'menu_name'   => 'required',
                    'menu_en_name'   => 'required',
                    'menu_sort'   => 'required|integer|min:0',
                ];
            }
            else
            {
                /*验证规则*/
                $rules = [
                    'menu_id'     => 'required|unique:system_menus,menu_id|integer',
                    'menu_name'   => 'required',
                    'menu_en_name'   => 'required',
                    'menu_sort'   => 'required|integer|min:0',
                    'menu_url'    => 'required',
                    'menu_controller'=> 'required',
                ];
            }

            $validator = Validator::make($request->all(), $rules);

            if($validator->passes() && $menu->addAdminMenu($request))
            {   /*验证通过*/
                $m3result->code    = 0;
                $m3result->messages= __('admin.success');
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
                $m3result->data  = $validator->messages();
            }
        }
        else if($request->input('menu_id') > 0)/*编辑栏目*/
        {

            if($request->input('parent_id') == 0)/*是否为一级栏目*/
            {
                /*验证规则*/
                $rules = [
                    'menu_id'     => [
                        'required',
                        'integer',
                        Rule::exists('system_menus')->where(function ($query) {
                            $query->where('menu_id',$GLOBALS['request']->input('menu_id'));
                        }),
                    ],
                    'menu_name'   => 'required',
                    'menu_en_name'   => 'required',
                    'menu_sort'   => 'required|integer|min:0',
                ];
            }
            else
            {
                /*验证规则*/
                $rules = [
                    'menu_id'     => [
                        'required',
                        'integer',
                        Rule::exists('system_menus')->where(function ($query) {
                            $query->where('menu_id',$GLOBALS['request']->input('menu_id'));
                        }),
                    ],
                    'menu_name'   => 'required',
                    'menu_en_name'   => 'required',
                    'menu_sort'   => 'required|integer|min:0',
                    'menu_url'    => 'required',
                    'menu_controller'=> 'required',
                ];
            }

            $validator = Validator::make($request->all(), $rules);

            if($validator->passes() && $menu->editAdminMenu($request))
            {   /*验证通过*/
                $m3result->code    = 0;
                $m3result->messages= __('admin.success');
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
                $m3result->data  = $validator->messages();
            }
        }
        else
        {
            $m3result->code    = 2;
            $m3result->messages= '无效数据';
        }

        return $m3result->toJson();
    }


    /**
     * Ajax删除栏目提交
     * @param Request $request
     * @return \App\Tools\json
     */
    public function MenusDeleteOne(Request $request)
    {
        /*初始化*/
        $m3result = new M3Result();
        $menu     = new Menu();

        /*验证规则*/
        $rules = [
            'menu_id'     => [
                'required',
                'integer',
                Rule::exists('system_menus')->where(function ($query) {
                    $query->where('menu_id',$GLOBALS['request']->input('menu_id'));
                }),
            ]
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->passes() && $menu->deleteAdminMenu($request))
        {   /*验证通过*/
            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failed');
            $m3result->data['validator']  = $validator->messages();
        }

        return $m3result->toJson();
    }

    /**
     * 获取 系统菜单操作权限，根据请求判断返回页面或处理
     * @param Request $request
     * @return View or Json
     */
    public function MenusAccess(Request $request)
    {
        /*初始化*/
        $m3result = new M3Result();

        if($request->ajax())
        {
            if($_ENV['APP_KEY'] == $request->input('input_value'))
            {
                $m3result->code    = 0;
                $m3result->messages= __('admin.success');
                $request->session()->put('CheckMenusAccessAuthority','allow');
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
            }
            return $m3result->toJson();
        }
        else
        {
            $this->ViewData['layer_title']    = __('admin.menu.accessNotify');
            $this->ViewData['layer_formType'] = '1';
            $this->ViewData['layer_value']    = '';
            $this->ViewData['ajax_url']       = action('Admin\MenuController@MenusAccess');
            $this->ViewData['ajax_back']      = action('Admin\MenuController@MenusIndex');

            return view('admin.temp.access_input',$this->ViewData);
        }

    }


}