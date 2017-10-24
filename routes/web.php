<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */

Route::get('test', 'TestController@Index');
Route::get('test/add', 'TestController@T_add');
Route::get('test/list', 'TestController@T_list');
Route::get('test/update', 'TestController@T_update');
Route::get('test/delete', 'TestController@T_delete');


Route::get('/', 'IndexController@Index');/*后台主框架 | index */
Route::get('login', 'IndexController@Login');/*登录页面 | login */
Route::get('login/submit', 'IndexController@LoginSubmit');/*登录提交 */
Route::get('welcome', 'IndexController@Welcome');/*后台首页 | welcome */

Route::group(['group' => '平台'], function ()
{
//    Route::get('platform/need/list/{status?}/{create_time?}', 'ArmyController@NeedList')->name('军方需求列表');/*军方需求列表 | army_need_list */
//    Route::get('platform/need/view/{id?}', 'ArmyController@NeedView')->name('查看军方需求');/*查看军方需求 | army_need_view */
//    Route::any('platform/need/release', 'ArmyController@NeedRelease')->name('发布军方需求');/*发布军方需求*/
//    Route::any('platform/need/edit', 'ArmyController@NeedEdit')->name('修改军方需求');/*修改军方需求*/
//    Route::any('platform/need/delete', 'ArmyController@NeedDelete')->name('删除军方需求');/*删除军方需求*/

    Route::any('platform/need/release', 'PlatformController@NeedRelease')->name('平台发布需求');/*平台发布需求*/

});

Route::group(['group' => '军方'], function ()
{
    Route::get('army/need/list/{status?}/{create_time?}', 'ArmyController@NeedList')->name('军方需求列表');/*军方需求列表 | army_need_list */
    Route::get('army/need/view/{id?}', 'ArmyController@NeedView')->name('军方查看需求');/*军方查看需求 | army_need_view */
    Route::any('army/need/release', 'ArmyController@NeedRelease')->name('军方发布需求');/*军方发布需求*/
    Route::any('army/need/edit', 'ArmyController@NeedEdit')->name('军方修改需求');/*军方修改需求*/
    Route::any('army/need/delete', 'ArmyController@NeedDelete')->name('军方删除需求');/*军方删除需求*/
});

Route::group(['group' => '用户管理'], function ()
{
    Route::get('user/list/{identity?}/{is_disable?}/{nick_name?}/{phone?}', 'UserController@UserList')->name('用户列表');/*用户列表 | user_list */
    Route::get('log/list/{identity?}/{nick_name?}', 'UserController@LogList')->name('操作日志列表');/*操作日志列表 | log_list */
    Route::get('user/view/{id?}', 'UserController@UserView')->name('查看用户');/*查看用户 | user_view */
    Route::any('user/check/name', 'UserController@UserCheckName')->name('检测用户名占用');/*检测用户名占用*/
    Route::any('user/add', 'UserController@UserAdd')->name('新增用户');/*新增用户*/
    Route::any('user/edit', 'UserController@UserEdit')->name('修改用户');/*修改用户*/
    Route::any('user/enable', 'UserController@UserEnable')->name('启用用户');/*启用用户*/
    Route::any('user/disable', 'UserController@UserDisable')->name('禁用用户');/*禁用用户*/
});

Route::group(['group' => '商品管理'], function ()
{
    /*分类*/
    Route::get('category/list', 'ProductController@CategoryList')->name('商品分类列表');/*商品分类列表 | category_list */
    Route::get('category/view/{id?}', 'ProductController@CategoryView')->name('查看商品分类');/*查看商品分类 | category_view */
    Route::any('category/add', 'ProductController@CategoryAdd')->name('新增商品分类');/*新增商品分类*/
    Route::any('category/edit', 'ProductController@CategoryEdit')->name('修改商品分类');/*修改商品分类*/
    Route::any('category/delete', 'ProductController@CategoryDelete')->name('删除商品分类');/*删除商品分类*/
    /*商品*/
    Route::get('product/list', 'ProductController@ProductList')->name('商品列表');/*商品列表 | product_list */
    Route::get('product/view/{id?}', 'ProductController@ProductView')->name('查看商品');/*查看商品 | product_view */
    Route::any('product/add', 'ProductController@ProductAdd')->name('新增商品');/*新增商品*/
    Route::any('product/edit', 'ProductController@ProductEdit')->name('修改商品');/*修改商品*/
    Route::any('product/delete', 'ProductController@ProductDelete')->name('删除商品');/*删除商品*/
});

Route::group(['group' => '用户中心'], function ()
{
    Route::get('password/original/view', 'UserController@PasswordOriginalView')->name('查看修改密码');/*查看修改密码 | password_original */
    Route::any('password/original/edit', 'UserController@PasswordOriginalEdit')->name('修改密码');/*修改密码*/
});


//Route::get ('no/privilege','IndexController@NoPrivilege');/*没有权限页面*/
//Route::get ('login','IndexController@Login');/*登录页面*/
//Route::get ('logout','IndexController@Logout');/*退出登录*/
//Route::post('login/submit','IndexController@LoginSubmit');/*登录处理*/
//Route::get ('welcome','IndexController@Welcome');/*我的桌面*/
//Route::get ('language/{lang}','IndexController@SetLanguage');/*当前语言更改*/
//Route::post('tools/image_preview','ToolsController@ImagePreview');/*上传临时图片,方便展示*/
//Route::post('tools/image_save','ToolsController@ImageSave');/*上传图片,永久保存*/
//Route::post('tools/image_attr_save','ToolsController@ImageAttrSave');/*上传属性小图,永久保存*/
//Route::post('setting/menus/get','MenuController@MenusGetOne');/*获取单个栏目信息*/
//Route::post('goods/category/relevance','GoodsController@CategoryGetRelevance');
