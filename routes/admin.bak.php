<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/15 0015
 * Time  : 16:02
 */

/**
 * Admin后台的路由
 */
Route::group(['namespace' => 'Admin','middleware'=>['AdminLanguage','AdminLoginAndPrivilege'] ], function ()
{
    Route::get ('/','IndexController@Index');/*后台主框架*/
    Route::get ('no/privilege','IndexController@NoPrivilege');/*没有权限页面*/
    Route::get ('login','IndexController@Login');/*登录页面*/
    Route::get ('logout','IndexController@Logout');/*退出登录*/
    Route::post('login/submit','IndexController@LoginSubmit');/*登录处理*/
    Route::get ('welcome','IndexController@Welcome');/*我的桌面*/
    Route::get ('language/{lang}','IndexController@SetLanguage');/*当前语言更改*/
    Route::post('tools/image_preview','ToolsController@ImagePreview');/*上传临时图片,方便展示*/
    Route::post('tools/image_save','ToolsController@ImageSave');/*上传图片,永久保存*/
    Route::post('tools/image_attr_save','ToolsController@ImageAttrSave');/*上传属性小图,永久保存*/
    Route::post('setting/menus/get','MenuController@MenusGetOne');/*获取单个栏目信息*/
    Route::post('goods/category/relevance','GoodsController@CategoryGetRelevance');


    /*商品管理*/
    Route::group(['group' => '商品管理,GoodsManage'], function ()
    {
        Route::group(['action_group' => '商品分类,GoodsCategory'], function ()
        {
            Route::get ('goods/category/index','GoodsController@CategoryIndex')->name('列表,List');
            Route::get ('goods/category/edit/{id?}','GoodsController@CategoryView')->name('查看,View')->where('id', '[0-9]+');
            Route::post('goods/category/submit','GoodsController@CategoryEditSubmit')->name('编辑,Edit');
            Route::post('goods/category/delete','GoodsController@CategoryDeleteOne')->name('删除,Delete');
        });

        Route::group(['action_group' => '商品品牌,GoodsBrand'], function ()
        {
            Route::get ('goods/brand/index/{category_id?}','GoodsController@BrandIndex')->name('列表,List')->where('category_id', '[0-9]+');
            Route::get ('goods/brand/edit/{id?}','GoodsController@BrandView')->name('查看,View')->where('id', '[0-9]+');
            Route::post('goods/brand/submit','GoodsController@BrandEditSubmit')->name('编辑,Edit');
            Route::post('goods/brand/delete','GoodsController@BrandDeleteOne')->name('删除,Delete');
//            Route::post('article/info/audit','ArticleController@InfoAudit')->name('审核,Audit');
        });

        Route::group(['action_group' => '商品属性,GoodsAttributes'], function ()
        {
            Route::get ('goods/attr/index/{category_id?}','GoodsController@AttributesIndex')->name('列表,List')->where('category_id', '[0-9]+');
            Route::get ('goods/attr/edit/{id?}','GoodsController@AttributesView')->name('查看,View')->where('id', '[0-9]+');
            Route::post('goods/attr/submit','GoodsController@AttributesEditSubmit')->name('编辑,Edit');
            Route::post('goods/attr/delete','GoodsController@AttributesDeleteOne')->name('删除,Delete');
        });

        Route::group(['action_group' => '商品列表,GoodsList'], function ()
        {
            Route::get ('goods/product/index/{category_id?}','GoodsController@GoodsIndex')->name('列表,List')->where('category_id', '[0-9]+');
            Route::get ('goods/product/edit/{id?}','GoodsController@GoodsView')->name('查看,View')->where('id', '[0-9]+');
            Route::post('goods/product/add','GoodsController@GoodsAdd')->name('商品添加,Goods Add');
            Route::post('goods/product/modify','GoodsController@GoodsEdit')->name('商品编辑,Goods Edit');
            Route::post('goods/product/audit','GoodsController@GoodsAudit')->name('商品审核,Goods Audit');
            Route::post('goods/product/delete','GoodsController@GoodsDeleteOne')->name('删除,Delete');
        });

        Route::group(['action_group' => '商品回收站,GoodsRecycle'], function ()
        {
            Route::get ('goods/recycle/index','GoodsController@GoodsRecycleIndex')->name('列表,List');
            Route::post('goods/recycle/destroy','GoodsController@GoodsRecycleDestroyOne')->name('销毁,Destroy');
            Route::post('goods/recycle/recovery','GoodsController@GoodsRecycleRecovery')->name('恢复,Recovery');;
        });

    });

    /*短信管理*/
    Route::group(['group' => '短信管理,SmsManage'], function ()
    {
        Route::group(['action_group' => '短信设置,SmsSetting'], function ()
        {
            Route::get ('sms/setting/edit','SmsController@SettingView')->name('查看,View');/*添加编辑页面*/
            Route::post('sms/setting/submit','SmsController@SettingEditSubmit')->name('编辑,Edit');/*添加编辑提交*/
        });
        Route::group(['action_group' => '模板设置,TemplateSetting'], function ()
        {
            Route::get ('sms/template/index','SmsController@TemplateIndex')->name('查看,List');/*添加编辑页面*/
            Route::any ('sms/template/fuzzyQuery','SmsController@TemplateFuzzyQuery')->name('搜索,Query');/*搜索短信*/
            Route::get ('sms/template/edit/{id?}','SmsController@TemplateView')->name('查看,View')->where('id', '[0-9]+');/*添加编辑页面*/
            Route::post('sms/template/submit','SmsController@TemplateEditSubmit')->name('编辑,Edit');/*添加编辑提交*/
            Route::post('sms/template/del','SmsController@TemplateDeleteOne')->name('删除,Delete');/*删除*/
            Route::post('sms/template/sendSms','SmsController@TemplateSendSms')->name('发送短信,send');/*发送短信测试*/
        });

    });

    /*广告管理*/
    Route::group(['group' => '广告管理,AdvertManage'], function ()
    {
        Route::group(['action_group' => '广告位置管理,PositionManage'], function ()
        {
            Route::get ('picture/position/index','AdvertController@PositionIndex')->name('列表,List');/*广告位列表列表*/
            Route::get ('picture/position/edit/{id?}','AdvertController@PositionView')->name('查看,View')->where('id', '[0-9]+');/*添加编辑页面*/
            Route::any ('picture/position/fuzzyQuery','AdvertController@PositionFuzzyQuery')->name('广告位置模糊查询,query');/*广告位模糊查询*/
            Route::post('picture/position/submit','AdvertController@PositionSubmit')->name('编辑,Edit');/*添加编辑提交*/
            Route::post('picture/position/delete','AdvertController@PositionDelOne')->name('删除,Delete');/*删除一条广告位*/
            Route::post('picture/position/disable','AdvertController@PositionDisable')->name('禁用,AdPositionDisable');/*禁用一条广告位*/
            Route::post('picture/position/quick','AdvertController@PositionQuickEdit')->name('快捷编辑,Quick Edit');
        });
        Route::group(['action_group' => '广告管理,AdvertEntityManage'], function ()
        {
            Route::get ('picture/entity/edit/{id?}','AdvertController@EntityView')->name('查看,View')->where('id', '[0-9]+');/*添加编辑页面*/
            Route::post('picture/entity/submit','AdvertController@EntitySubmit')->name('编辑,Edit');/*添加编辑提交*/
            Route::post('picture/entity/delete','AdvertController@EntityDelOne')->name('删除,Delete');/*删除一条广告*/
        });
        /*广告位显示广告测试*/
        Route::get ('picture/display/test','AdvertController@AdvertDisplayTest');
    });

    /*邮箱管理*/
    Route::group(['group' => '邮箱管理,EmailManage'], function ()
    {
        Route::group(['action_group' => '服务器设置,ServerManage'], function ()
        {
            Route::get ('email/server/edit','EmailController@ServerView')->name('更新,Update');
            Route::post('email/server/submit','EmailController@ServerEditSubmit')->name('编辑,Edit');
        });
        Route::group(['action_group' => '模板管理,TemplateManage'], function ()
        {
            Route::get ('email/template/edit','EmailController@TemplateView')->name('更新,Update');
            Route::get ('email/template/getOneTemplate/{id?}','EmailController@GetOneTemplateById')->where('id', '[0-9]+');
            Route::post('email/template/submit','EmailController@TemplateEditSubmit')->name('编辑,Edit');
            Route::post('email/send/test','EmailController@SendTest');
        });
    });

    /*系统管理*/
    Route::group(['group' => '系统管理,SystemManage'], function ()
    {
        Route::group(['action_group' => '系统设置,SystemSetting'], function ()
        {
            Route::get('setting/config/index', 'ConfigController@ConfigIndex')->name('查看,View');/*设置页面*/
            Route::post('setting/config/submit', 'ConfigController@ConfigSubmit')->name('编辑,Edit');/*设置提交处理*/
        });

        Route::group(['action_group' => '栏目管理,MenusManage' , 'middleware' => 'CheckMenusAccessAuthority'], function ()/*中间件说明:验证栏目操作权限*/
        {
            Route::any ('setting/menus/access','MenuController@MenusAccess')->name('验证,Access');/*验证*/
            Route::get ('setting/menus/index','MenuController@MenusIndex')->name('列表,List');/*列表*/
            Route::get ('setting/menus/edit/{id?}','MenuController@MenusView')->name('查看,View')->where('id', '[0-9]+');/*添加编辑页面*/
            Route::post('setting/menus/submit','MenuController@MenusEditSubmit')->name('编辑,Edit');/*添加编辑提交*/
            Route::post('setting/menus/delete','MenuController@MenusDeleteOne')->name('删除,Delete');/*删除栏目*/
        });
    });

    /*权限管理*/
    Route::group(['group' => '权限管理,PrivilegeManage'], function ()
    {
        Route::group(['action_group' => '角色管理,RoleManage'], function ()
        {
            Route::post('privilege/update','PrivilegeController@PrivilegeUpdate')->name('更新,Update');
            Route::get ('privilege/role/index','PrivilegeController@RoleIndex')->name('列表,List');
            Route::get ('privilege/role/edit/{id?}','PrivilegeController@RoleView')->name('查看,View')->where('id', '[0-9]+');
            Route::post('privilege/role/submit','PrivilegeController@RoleEditSubmit')->name('编辑,Edit');
            Route::post('privilege/role/delete','PrivilegeController@RoleDeleteOne')->name('删除,Delete');

        });

        Route::group(['action_group' => '管理员管理,ManagerManage'], function ()
        {
            Route::get ('privilege/manager/index','PrivilegeController@ManagerIndex')->name('列表,List');
            Route::get ('privilege/manager/edit/{id?}','PrivilegeController@ManagerView')->name('查看,View')->where('id', '[0-9]+');
            Route::post('privilege/manager/submit','PrivilegeController@ManagerEditSubmit')->name('编辑,Edit');
            Route::post('privilege/manager/delete','PrivilegeController@ManagerDeleteOne')->name('删除,Delete');
            Route::post('privilege/manager/quick','PrivilegeController@ManagerQuickEdit')->name('快捷编辑,Quick Edit');
        });

        Route::group(['action_group' => '管理员日志,ManagerLog'], function ()
        {
            Route::get ('privilege/dialog/index/{admin_id?}','PrivilegeController@AdminLogIndex')->name('列表,List')->where('admin_id', '[0-9]+');
            Route::post('privilege/dialog/delete','PrivilegeController@AdminLogBatchDelete')->name('批量删除,Batch Delete');
        });

    });

    /*文章管理*/
    Route::group(['group' => '文章管理,ArticleManage'], function ()
    {
        Route::group(['action_group' => '文章分类,ArticleCategory'], function ()
        {
            Route::get ('article/category/index','ArticleController@CategoryIndex')->name('列表,List');
            Route::get ('article/category/edit/{id?}','ArticleController@CategoryView')->name('查看,View')->where('id', '[0-9]+');
            Route::post('article/category/submit','ArticleController@CategoryEditSubmit')->name('编辑,Edit');
            Route::post('article/category/delete','ArticleController@CategoryDeleteOne')->name('删除,Delete');
        });

        Route::group(['action_group' => '文章列表,ArticleList'], function ()
        {
            Route::get ('article/info/index/{category_id?}','ArticleController@InfoIndex')->name('列表,List')->where('category_id', '[0-9]+');
            Route::get ('article/info/edit/{id?}','ArticleController@InfoView')->name('查看,View')->where('id', '[0-9]+');
            Route::post('article/info/submit','ArticleController@InfoEditSubmit')->name('编辑,Edit');
            Route::post('article/info/audit','ArticleController@InfoAudit')->name('审核,Audit');
            Route::post('article/info/delete','ArticleController@InfoDeleteOne')->name('删除,Delete');
        });
    });

});
