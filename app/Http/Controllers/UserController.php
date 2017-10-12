<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */
namespace App\Http\Controllers;

use App\Entity\Users;
use App\Models\User;
use App\Tools\M3Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * 用户控制器
 * Class IndexController
 * @package App\Http\Controllers\Admin
 */
class UserController extends Controller
{
    public $ViewData = array(); /*传递页面的数组*/

    public function UserList()
    {
        /*初始化*/
        $user = new User();

//        $category  = new Category();
//        $brand     = new Brand();
//        $this->ViewData['brand_list'] = array();
//        $this->ViewData['nav_position']  = Menu::getAdminPosition();/*面包屑*/
//        $this->ViewData['category_tree'] = $category->getGoodsCategoryTree('brand');

//        if($category_id > 0)
//        {
//            $this->ViewData['category_info'] = $category->getOneCategoryRelationBrand($category_id);
//            $this->ViewData['brand_list'] =  $brand->getGoodsBrandList([['category_id',$category_id]]);
//        }
//        else
//        {
//            $this->ViewData['brand_list'] = $brand->getGoodsBrandList();
//        }

        return view('admin.goods_brand_index', $this->ViewData);
    }

    public function UserView($id = 0)
    {

    }

    public function UserDelete(Request $request)
    {

    }

    public function UserAdd(Request $request)
    {
        /*初始化*/
        $user = new User();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'nick_name' => 'required',
            'phone' => [
                'required',
                'numeric',
                'regex:/^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8])|(19[7]))\d{8}$/',
            ],
            'identity' => [
                'required',
                'integer',
                Rule::in([User::ARMY_ADMIN, User::PLATFORM_ADMIN, User::SUPPLIER_ADMIN]),
            ],
            'user_name' => 'required|unique:users,user_name'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $user->addUser($request->all()))
        {   /*验证通过并且添加成功*/
            $m3result->code = 0;
            $m3result->messages = '用户添加成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['user'] = $user->messages();
        }

        return $m3result->toJson();
    }

    public function UserEdit(Request $request)
    {
        /*初始化*/
        $user = new User();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'nick_name' => 'required',
            'phone' => [
                'required',
                'numeric',
                'regex:/^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8])|(19[7]))\d{8}$/',
            ],
            'identity' => [
                'required',
                'integer',
                Rule::in([User::ARMY_ADMIN, User::PLATFORM_ADMIN, User::SUPPLIER_ADMIN]),
            ],
            'user_name' => 'required|unique:users,user_name'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes() && $user->addUser($request->all()))
        {   /*验证通过并且添加成功*/
            $m3result->code = 0;
            $m3result->messages = '用户添加成功';
        }
        else
        {
            $m3result->code = 1;
            $m3result->messages = '数据验证失败';
            $m3result->data['validator'] = $validator->messages();
            $m3result->data['user'] = $user->messages();
        }

        return $m3result->toJson();
    }

}