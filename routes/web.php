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
    Route::get('user/list', 'UserController@UserList')->name('用户列表');/*用户列表*/
    Route::get('user/view/{id?}', 'UserController@UserView')->name('用户查看');/*用户查看*/
    Route::any('user/add', 'UserController@UserAdd')->name('用户新增');/*用户新增*/
    Route::any('user/submit', 'UserController@UserEdit')->name('用户修改');/*用户修改*/
    Route::any('user/delete', 'UserController@UserDelete')->name('用户删除');/*用户删除*/
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
