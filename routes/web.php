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


Route::get('login', 'IndexController@Login');/*登录页面*/
Route::get('/', 'IndexController@Index');/*后台主框架*/
Route::get('welcome', 'IndexController@Welcome');/*后台首页*/


Route::group(['group' => '用户管理'], function ()
{
    Route::get('user/list/{identity?}/{is_disable?}/{nick_name?}/{phone?}', 'UserController@UserList')->name('用户列表');/*用户列表*/
    Route::get('log/list/{identity?}/{nick_name?}', 'UserController@LogList')->name('操作日志列表');/*操作日志列表*/
    Route::get('user/view/{id?}', 'UserController@UserView')->name('查看用户');/*查看用户*/
    Route::any('user/add', 'UserController@UserAdd')->name('新增用户');/*新增用户*/
    Route::any('user/edit', 'UserController@UserEdit')->name('修改用户');/*修改用户*/
    Route::any('user/enable', 'UserController@UserEnable')->name('启用用户');/*启用用户*/
    Route::any('user/disable', 'UserController@UserDisable')->name('禁用用户');/*禁用用户*/
});

Route::group(['group' => '用户中心'], function ()
{
    Route::get('password/original/view', 'UserController@PasswordOriginalView')->name('查看修改密码');/*查看修改密码*/
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
