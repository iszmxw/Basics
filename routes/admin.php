<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| 开始写路由吧
|
*/
Route::group(['namespace' => 'Admin', 'middleware' => 'admin'], function () {
    // 后台首页
    Route::any('/', 'DashboardController@dashboard')
        ->name('admin.dashboard');
    // 登录系统
    Route::any('login', 'LoginController@login')
        ->name('admin.login');
    // 登录检测
    Route::any('login_check', 'LoginController@login_check')
        ->name('admin.login_check');
    // 登录检测
    Route::any('error_page', 'LoginController@error_page')
        ->name('admin.error_page');
    // 退出系统
    Route::any('quit', 'LoginController@quit')
        ->name('admin.quit');
    // 系统首页
    Route::any('dashboard', 'DashboardController@dashboard')
        ->name('admin.dashboard');

    // 系统管理
    Route::group(['prefix' => 'system'], function () {

        // 个人中心
        Route::any('profile', 'DashboardController@profile')->name('admin.system.profile');
        // 编辑个人资料
        Route::any('profile_edit', 'DashboardController@profile_edit')->name('admin.system.profile_edit');


        // 菜单路由管理页面
        Route::any('menu_route', 'MenuRouteController@menu_route')->name('admin.system.menu_route');
        // 添加菜单或者路由
        Route::any('route_add', 'MenuRouteController@route_add')->name('admin.system.route_add');
        // 删除菜单或者路由
        Route::any('route_delete', 'MenuRouteController@route_delete')->name('admin.system.route_delete');
        // 编辑菜单或者路由
        Route::any('route_edit', 'MenuRouteController@route_edit')->name('admin.system.route_edit');
        // 菜单排序
        Route::any('route_order', 'MenuRouteController@route_order')->name('admin.system.route_order');
        // 获取菜单路由信息
        Route::any('route_info', 'MenuRouteController@route_info')->name('admin.system.route_info');


        // 角色管理
        // 增加角色
        Route::any('role_add', 'AuthorityController@role_add')->name('admin.system.role_add');
        // 获取树形路由数据
        Route::any('tree_data', 'AuthorityController@tree_data')->name('admin.system.tree_data');
        // 异步获取添加模态框
        Route::any('role_modal_add', 'AuthorityController@role_modal_add')->name('admin.system.role_modal_add');
        // 删除角色
        Route::any('role_delete', 'AuthorityController@role_delete')->name('admin.system.role_delete');
        // 边界角色
        Route::any('role_edit', 'AuthorityController@role_edit')->name('admin.system.role_edit');
        // 异步获取编辑模态框
        Route::any('role_modal_edit', 'AuthorityController@role_modal_edit')->name('admin.system.role_modal_edit');
        // 获取角色列表
        Route::any('role_list', 'AuthorityController@role_list')->name('admin.system.role_list');
        // 系统管理员
        Route::any('account_add', 'AccountController@account_add')->name('admin.system.account_add');
        // 账号状态修改
        Route::any('account_status', 'AccountController@account_status')->name('admin.system.account_status');
        // 编辑管理员信息
        Route::any('account_edit', 'AccountController@account_edit')->name('admin.system.account_edit');
        // 获取单个管理员信息
        Route::any('account_info', 'AccountController@account_info')->name('admin.system.account_info');
        // 系统管理员列表
        Route::any('account_list', 'AccountController@account_list')->name('admin.system.account_list');
    });

    // 广告市场管理
    Route::group(['prefix' => 'advert'], function () {
        // 上传广告文件
        Route::any('uploads', 'AdvertMarketController@uploads')->name('admin.advert.uploads');
        // 删除广告文件
        Route::any('delete_file', 'AdvertMarketController@delete_file')->name('admin.advert.delete_file');
        // 添加广告信息
        Route::any('advert_add', 'AdvertMarketController@advert_add')->name('admin.advert.advert_add');
        Route::any('advert_add_data', 'AdvertMarketController@advert_add_data')->name('admin.advert.advert_add_data');
        // 修改广告状态
        Route::any('advert_status', 'AdvertMarketController@advert_status')->name('admin.advert.advert_status');
        // 编辑广告信息
        Route::any('advert_edit', 'AdvertMarketController@advert_edit')->name('admin.advert.advert_edit');
        // 编辑广告信息
        Route::any('advert_edit_data', 'AdvertMarketController@advert_edit_data')->name('admin.advert.advert_edit_data');
        // 广告市场列表
        Route::any('advert_list', 'AdvertMarketController@advert_list')->name('admin.advert.advert_list');
    });


    // 商户管理
    Route::group(['prefix' => 'merchant'], function () {
        // 添加合作商户
        Route::any('merchant_add', 'MerchantController@merchant_add')->name('admin.merchant.merchant_add');
        // 修改合作商户状态
        Route::any('merchant_status', 'MerchantController@merchant_status')->name('admin.merchant.merchant_status');
        // 编辑合作商户
        Route::any('merchant_edit', 'MerchantController@merchant_edit')->name('admin.merchant.merchant_edit');
        // 获取单个合作商户信息
        Route::any('merchant_info', 'MerchantController@merchant_info')->name('admin.merchant.merchant_info');
        // 商户列表
        Route::any('merchant_list', 'MerchantController@merchant_list')->name('admin.merchant.merchant_list');
        // 合作商户设备列表
        Route::any('device_list', 'MerchantController@device_list')->name('admin.merchant.device_list');
        // 合作商户设备状态更改
        Route::any('device_status', 'MerchantController@device_status')->name('admin.merchant.device_status');
        // 编辑合作商设备
        Route::any('device_edit', 'MerchantController@device_edit')->name('admin.merchant.device_edit');
        // 合作商的单个设备信息获取
        Route::any('device_info', 'MerchantController@device_info')->name('admin.merchant.device_info');
    });

    // 运营监控
    Route::group(['prefix' => 'monitor'], function () {
        // 广告订单监控
        Route::any('advert_order', 'MonitorController@advert_order')->name('admin.monitor.advert_order');
        // track_url上报记录
        Route::any('track_log', 'MonitorController@track_log')->name('admin.monitor.track_log');
    });
});

