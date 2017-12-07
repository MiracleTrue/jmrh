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

/*不需要登录的请求*/
Route::get('login', 'IndexController@Login');/*登录页面 | login */
Route::any('login/submit', 'IndexController@LoginSubmit');/*登录提交 */

/*需要登录的请求*/
Route::group(['middleware' => ['WebLoginAndPrivilege']], function ()
{
    Route::group(['group' => '用户中心', 'identity' => [\App\Models\User::PLATFORM_ADMIN, \App\Models\User::SUPPLIER_ADMIN, \App\Models\User::ARMY_ADMIN]], function ()
    {
        Route::get('/', 'IndexController@Index');/*后台主框架 | index */
        Route::get('logout', 'IndexController@Logout');/*退出登录提交 */
        Route::get('welcome', 'IndexController@Welcome');/*后台首页 | welcome */
        Route::get('log/manage', 'UserController@LogManage')->name('用户操作日志');/*用户操作日志 | log_manage */
        Route::get('product/show/{id}', 'ProductController@ProductShow')->name('商品详情');/*商品详情页面 | product_show */
        Route::get('password/original/view', 'UserController@PasswordOriginalView')->name('查看修改密码');/*查看修改密码 | password_original */
        Route::any('password/original/edit', 'UserController@PasswordOriginalEdit')->name('修改密码');/*修改密码*/
        Route::any('product/ajax/list', 'ProductController@ProductAjaxList')->name('获取商品列表');/*获取商品列表*/
    });

    Route::group(['group' => '平台', 'identity' => [\App\Models\User::PLATFORM_ADMIN]], function ()
    {
        Route::get('platform/need/list/{type?}/{status?}/{create_time?}', 'PlatformController@NeedList')->name('订单列表');/*平台订单列表 | platform_need_list */
        Route::get('platform/need/view', 'PlatformController@NeedView')->name('发布需求页面');/*平台发布需求页面 | platform_need_view */
        Route::get('platform/allocation/view/{order_id}', 'PlatformController@OfferAllocationView')->name('分配供应商页面');/*分配供应商页面 | platform_allocation_view*/
        Route::get('platform/offer/view/{order_id}', 'PlatformController@OfferInfoView')->name('查看报价页面');/*选择供应商页面 | platform_offer_view*/
        Route::any('platform/need/release', 'PlatformController@NeedRelease')->name('发布需求');/*平台发布需求*/
        Route::any('platform/allocation/offer', 'PlatformController@OfferAllocation')->name('分配供应商');/*平台供应商分配*/
        Route::any('platform/selected/offer', 'PlatformController@OfferSelected')->name('选择供应商');/*平台供应商选择*/
        Route::any('platform/inventory/supply', 'PlatformController@InventorySupply')->name('库存供应');/*平台库存供应*/
        Route::any('platform/confirm/receive', 'PlatformController@ConfirmReceive')->name('供应商确认收货');/*供应商确认收货*/
        Route::any('platform/send/army', 'PlatformController@SendArmy')->name('发货到军方');/*发货到军方*/
    });

    Route::group(['group' => '军方', 'identity' => [\App\Models\User::ARMY_ADMIN]], function ()
    {
        Route::get('army/need/list/{status?}/{create_time?}', 'ArmyController@NeedList')->name('需求列表');/*军方需求列表 | army_need_list */
        Route::get('army/need/view/{id?}', 'ArmyController@NeedView')->name('查看需求');/*军方查看需求 | army_need_view */
        Route::any('army/need/release', 'ArmyController@NeedRelease')->name('发布需求');/*军方发布需求*/
        Route::any('army/need/edit', 'ArmyController@NeedEdit')->name('修改需求');/*军方修改需求*/
        Route::any('army/need/delete', 'ArmyController@NeedDelete')->name('删除需求');/*军方删除需求*/
        Route::any('army/confirm/receive', 'ArmyController@ConfirmReceive')->name('确认收货');/*军方删除需求*/
    });

    Route::group(['group' => '供应商', 'identity' => [\App\Models\User::SUPPLIER_ADMIN]], function ()
    {
        Route::get('supplier/need/list/{status?}/{create_time?}', 'SupplierController@NeedList')->name('需求列表');/*供货商需求列表 | supplier_need_list */
        Route::get('supplier/offer/view/{offer_id}', 'SupplierController@OfferView')->name('报价页面');/*报价页面 | supplier_offer_view */
        Route::any('supplier/offer/submit', 'SupplierController@OfferSubmit')->name('报价');/*报价提交*/
        Route::any('supplier/send/goods', 'SupplierController@SendGoods')->name('配货');/*供应商配货*/
    });


    Route::group(['group' => '用户管理', 'identity' => [\App\Models\User::ADMINISTRATOR]], function ()
    {
        Route::get('log/list/{identity?}/{nick_name?}', 'UserController@LogList')->name('全部日志列表');/*全部日志列表 | log_list */
        Route::get('user/list/{identity?}/{is_disable?}/{nick_name?}/{phone?}', 'UserController@UserList')->name('用户列表');/*用户列表 | user_list */
        Route::get('user/view/{id?}', 'UserController@UserView')->name('查看用户');/*查看用户 | user_view */
        Route::any('user/check/name', 'UserController@UserCheckName')->name('检测用户名占用');/*检测用户名占用*/
        Route::any('user/add', 'UserController@UserAdd')->name('新增用户');/*新增用户*/
        Route::any('user/edit', 'UserController@UserEdit')->name('修改用户');/*修改用户*/
        Route::any('user/enable', 'UserController@UserEnable')->name('启用用户');/*启用用户*/
        Route::any('user/disable', 'UserController@UserDisable')->name('禁用用户');/*禁用用户*/
    });

    Route::group(['group' => '分类管理', 'identity' => [\App\Models\User::ADMINISTRATOR]], function ()
    {
        Route::get('category/list', 'ProductController@CategoryList')->name('商品分类列表');/*商品分类列表 | category_list */
        Route::get('category/view/{id?}', 'ProductController@CategoryView')->name('查看商品分类');/*查看商品分类 | category_view */
        Route::any('category/add', 'ProductController@CategoryAdd')->name('新增商品分类');/*新增商品分类*/
        Route::any('category/edit', 'ProductController@CategoryEdit')->name('修改商品分类');/*修改商品分类*/
        Route::any('category/delete', 'ProductController@CategoryDelete')->name('删除商品分类');/*删除商品分类*/
        Route::any('category/is/index', 'ProductController@CategoryIsIndex')->name('分类开启首页显示');/*分类首页显示*/
        Route::any('category/no/index', 'ProductController@CategoryNoIndex')->name('分类取消首页显示');/*分类取消首页显示*/
    });

    Route::group(['group' => '商品管理', 'identity' => [\App\Models\User::PLATFORM_ADMIN]], function ()
    {
        /*商品*/
        Route::get('product/list/{category_id?}', 'ProductController@ProductList')->name('商品列表');/*商品列表 | product_list */
        Route::get('product/add', 'ProductController@ProductAddPage')->name('新增商品页面');/*新增商品页面 | product_add */
        Route::get('product/edit/{product_id?}', 'ProductController@ProductEditPage')->name('修改商品页面')->where('product_id', '[0-9]+');/*修改商品页面 | product_edit */
        Route::any('product/add/submit', 'ProductController@ProductAddSubmit')->name('新增商品');/*新增商品*/
        Route::any('product/edit/submit', 'ProductController@ProductEditSubmit')->name('修改商品');/*修改商品*/
        Route::any('product/delete', 'ProductController@ProductDelete')->name('删除商品');/*删除商品*/
        /*规格*/
        Route::any('product/spec/add', 'ProductController@ProductSpecAdd')->name('新增商品规格');/*新增商品规格*/
        Route::any('product/spec/edit', 'ProductController@ProductSpecEdit')->name('编辑商品规格');/*编辑商品规格*/
        Route::any('product/spec/delete', 'ProductController@ProductSpecDelete')->name('删除商品规格');/*删除商品规格*/
        /*供应商协议价*/
        Route::get('product/supplier/price/{spec_id}', 'ProductController@ProductSupplierPriceView')->name('规格协议价列表')->where('spec_id', '[0-9]+');/*规格协议价列表 | product_supplier_price */
        Route::any('product/supplier/price/add', 'ProductController@ProductSupplierPriceAdd')->name('新增供应商协议价');/*新增供应商协议价*/
        Route::any('product/supplier/price/edit', 'ProductController@ProductSupplierPriceEdit')->name('编辑供应商协议价');/*编辑供应商协议价*/
        Route::any('product/supplier/price/delete', 'ProductController@ProductSupplierPriceDelete')->name('删除供应商协议价');/*删除供应商协议价*/
    });

});




//Route::get ('no/privilege','IndexController@NoPrivilege');/*没有权限页面*/
//Route::get ('login','IndexController@Login');/*登录页面*/
//Route::get ('logout','IndexController@Logout');/*退出登录*/
//Route::any('login/submit','IndexController@LoginSubmit');/*登录处理*/
//Route::get ('welcome','IndexController@Welcome');/*我的桌面*/
//Route::get ('language/{lang}','IndexController@SetLanguage');/*当前语言更改*/
//Route::any('tools/image_preview','ToolsController@ImagePreview');/*上传临时图片,方便展示*/
//Route::any('tools/image_save','ToolsController@ImageSave');/*上传图片,永久保存*/
//Route::any('tools/image_attr_save','ToolsController@ImageAttrSave');/*上传属性小图,永久保存*/
//Route::any('setting/menus/get','MenuController@MenusGetOne');/*获取单个栏目信息*/
//Route::any('goods/category/relevance','GoodsController@CategoryGetRelevance');
