<?php

namespace App\Http\Controllers\Open;

use App\Models\LoginLog;
use App\Models\MerchantAdOrderTrackLog;
use App\Models\MerchantDevice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * 获取用户登录数据
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:10
     */
    public function login_log(Request $request)
    {
        $merchant  = $request->get('merchant');
        $limit     = $request->get('limit');
        $limit     = $limit ? $limit : 10;
        $login_log = LoginLog::getPaginate(['account_id' => $merchant['id'], 'type' => 2], [], $limit, 'id', 'DESC');
        return ['code' => 200, 'message' => 'success', 'data' => $login_log];
    }


    /**
     * 首页统计
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:11
     */
    public function statistics(Request $request)
    {
        $merchant           = $request->get('merchant');
        $data['device_num'] = MerchantDevice::getCount(['merchant_id' => $merchant['id']]);
        $data['advert_num'] = MerchantAdOrderTrackLog::getCount(['merchant_id' => $merchant['id']]);
        return ['code' => 200, 'message' => 'ok', 'data' => $data];
    }
}
