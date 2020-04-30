<?php

namespace App\Http\Controllers\Admin;

use App\Models\MerchantAdOrder;
use App\Models\MerchantAdOrderTrackLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MonitorController extends Controller
{
    /**
     * 广告订单
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:08
     */
    public function advert_order(Request $request)
    {
        $data['advert_order'] = MerchantAdOrder::getMonitorAdvertOrderPaginate([], ['merchant_adorder.*', 'merchant.company'], 10, 'id', 'DESC');
        return view('admin.monitor.advert_order', $data);
    }

    /**
     * 广告播放日志
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:09
     */
    public function track_log(Request $request)
    {
        $data['track_log'] = MerchantAdOrderTrackLog::getMonitorTrackLogPaginate([], ['merchant_adorder_track_log.*', 'merchant.company'], 10, 'id', 'DESC');
        return view('admin.monitor.track_log', $data);
    }
}
