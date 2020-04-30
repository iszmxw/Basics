<?php

/*
|--------------------------------------------------------------------------
| 十万粉路由
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['namespace' => 'ShiWanFen', 'middleware' => 'web'], function () {
    // 获取设备信息
    Route::get('get_device', 'MigrateController@get_device');
    // 导入设备信息
    Route::get('import_data', 'MigrateController@import_data');
    // 扩展测试
    Route::get('extension', 'MigrateController@extension');
});
