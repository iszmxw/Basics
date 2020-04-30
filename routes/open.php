<?php

/*
|--------------------------------------------------------------------------
| Open Routes
|--------------------------------------------------------------------------
|
| 开始写路由吧
|
*/
Route::group(['namespace' => 'Open', 'middleware' => 'open'], function () {

    // 登录
    Route::any('login', 'LoginController@login');
    // 退出
    Route::any('logout', 'LoginController@logout');


    // 首页统计
    Route::any('statistics', 'DashboardController@statistics');
    // 登录日志
    Route::any('login_log', 'DashboardController@login_log');

    // 个人中心
    Route::group(['prefix' => 'account'], function () {
        // 获取账号信息
        Route::any('info', 'AccountController@info');
        // 编辑账号信息
        Route::any('info_edit', 'AccountController@info_edit');
        // 获取基本配置信息
        Route::any('basic', 'AccountController@basic');
        // 刷新appkey
        Route::any('Refresh', 'AccountController@Refresh');
        // 获取合作商户的结算信息
        Route::any('settlement_info', 'AccountController@settlement_info');
        // 编辑合作商户的结算信息
        Route::any('edit_settlement_info', 'AccountController@edit_settlement_info');
    });

    // 个人中心
    Route::group(['prefix' => 'sms'], function () {
        // 阿里大鱼服务
        Route::any('ali_sms', 'SmsController@ali_sms');
        // 通过登录身份中的手机号码获取验证码
        Route::any('get_code', 'SmsController@get_code');
        // 通过手机号码获取验证码
        Route::any('get_mobile_code', 'SmsController@get_mobile_code');
    });


    // 设备管理
    Route::group(['prefix' => 'device'], function () {
        // 设备列表
        Route::any('device_list', 'DeviceController@device_list');

        // 获取登录合作商的所有设备UUID
        Route::any('device_uuid', 'DeviceController@device_uuid');
    });


    Route::group(['prefix' => 'iszmxw'], function () {
        // 获取广告
        Route::any('get_ad', 'IszmxwController@get_ad');
        // 获取签名
        Route::any('sign', 'IszmxwController@sign');
        // 上传广告
        Route::any('uploads', 'IszmxwController@uploads');

        // 测试redis
        Route::any('set_redis', 'IszmxwController@set_redis');
        Route::any('get_redis', 'IszmxwController@get_redis');

    });

    // 广告
    Route::group(['prefix' => 'develop'], function () {
        // 获取广告市场
        Route::any('get_advert', 'OpenController@get_advert');
        // 广告上报
        Route::any('track/{track_id}.vue', 'OpenController@track_url');
        // 获取省份
        Route::any('get_province', 'AreaController@get_province');
        // 获取城市
        Route::any('get_city', 'AreaController@get_city');
        // 获取区域
        Route::any('get_area', 'AreaController@get_area');
    });

    // 广告相关
    Route::group(['prefix' => 'advert'], function () {
        // 广告市场
        Route::any('advert_market', 'MerchantAdvertController@advert_market');
        // 广告绑定播放的设备
        Route::any('add_merchant_advert', 'MerchantAdvertController@add_merchant_advert');
        // 广告添加绑定单个设备
        Route::any('advert_device_add', 'MerchantAdvertController@advert_device_add');
        // 获取广告绑定设备记录列表
        Route::any('advert_bind_list', 'MerchantAdvertController@advert_bind_list');
        // 获取单条广告绑定的设备
        Route::any('get_advert_bind', 'MerchantAdvertController@get_advert_bind');
        // 从广告中删除绑定的单个设备
        Route::any('advert_device_delete', 'MerchantAdvertController@advert_device_delete');
        // 获取广告记录
        Route::any('advert_log', 'MerchantAdvertController@advert_log');
    });
});
