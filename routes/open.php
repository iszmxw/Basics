<?php

/*
|--------------------------------------------------------------------------
| Open Routes
|--------------------------------------------------------------------------
|
| 开始写路由吧
|
*/

Route::group(['prefix' => 'iszmxw', 'namespace' => 'Open'], function () {
    // 获取uuid
    Route::any('create_company', 'IszmxwController@create_company');
    // 获取uuid
    Route::any('uuid', 'IszmxwController@uuid');
    // 获取广告
    Route::any('get_ad', 'IszmxwController@get_ad');
    // 获取签名
    Route::any('sign', 'IszmxwController@sign');
    // 上传广告
    Route::any('uploads', 'IszmxwController@uploads');
    // 获取上传到阿里云服务器的文件
    Route::any('get_file', 'IszmxwController@get_file');
    // 查看所有session
    Route::any('session_all', 'IszmxwController@session_all');
});

//
Route::group(['prefix' => 'ads', 'namespace' => 'Open'], function () {
    // 获取广告
    Route::any('get', 'AdsController@get');
    // 获取广告
    Route::any('track/{track_id}.vue', 'AdsController@track_url');
});

