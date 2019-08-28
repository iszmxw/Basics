<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| 开始写路由吧
|
*/


// 登录系统
Route::any('login', 'Admin\LoginController@login')
    ->name('admin.login');
// 登录检测
Route::any('login_check', 'Admin\LoginController@login_check')
    ->name('admin.login_check');
// 登录检测
Route::any('error_page', 'Admin\LoginController@error_page')
    ->name('admin.error_page');
// 退出系统
Route::any('quit', 'Admin\LoginController@quit')
    ->name('admin.quit');
// 系统首页
Route::any('dashboard', 'Admin\DashboardController@dashboard')
    ->name('admin.dashboard');


// 系统管理
Route::group(['prefix' => 'system', 'namespace' => 'Admin'], function () {

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
    // 增
    Route::any('role_add', 'AuthorityController@role_add')->name('admin.system.role_add');
    // 获取树形路由数据
    Route::any('tree_data', 'AuthorityController@tree_data')->name('admin.system.tree_data');
    // 异步获取添加模态框
    Route::any('role_modal_add', 'AuthorityController@role_modal_add')->name('admin.system.role_modal_add');
    // 删
    Route::any('role_delete', 'AuthorityController@role_delete')->name('admin.system.role_delete');
    // 改
    Route::any('role_edit', 'AuthorityController@role_edit')->name('admin.system.role_edit');
    // 异步获取编辑模态框
    Route::any('role_modal_edit', 'AuthorityController@role_modal_edit')->name('admin.system.role_modal_edit');
    // 查
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

