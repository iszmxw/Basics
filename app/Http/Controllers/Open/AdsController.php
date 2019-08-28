<?php

namespace App\Http\Controllers\Open;

use App\Http\Requests\GetVideo;
use App\Models\AdInfo;
use App\Models\CompanyAdOrder;
use App\Http\Controllers\Controller;
use App\Models\CompanyAdOrderTrackLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AdsController extends Controller
{
    public $redis;

    public function __construct()
    {
        $this->redis = Redis::connection('ads');
    }

    /**
     * 获取广告接口
     * @param GetVideo $request
     * @return array
     * @throws \Exception
     */
    public function get(GetVideo $request)
    {
        $sign = $request->get('sign');                 // 本次请求的签名
        $appid = $request->get('appid');               // 合作商的appid
        $company = $request->get('company');           // 合作商的信息包含id和appid
        $device_uuid = $request->get('device_uuid');   // 合作商的设备uuid
        $ad_num = $request->get('ad_num');             // 请求广告数量
        $lng = $request->get('lng');                   // 经度
        $lat = $request->get('lat');                   // 纬度
        $ip = $request->ip();
        $adInfo = [];

        $count = CompanyAdOrder::getCount(['company_id' => $company['id'], 'device_uuid' => $device_uuid, 'status' => 0]);
        if ($count > 0) {// 第二次请求的时候检测是否有未消费的订单，有就作为过期处理
            CompanyAdOrder::EditData(['company_id' => $company['id'], 'device_uuid' => $device_uuid, 'status' => 0], ['status' => 2]);
        }


        // 根据合作商的设备条件获取广告
        $adList = AdInfo::getList();
        \DB::beginTransaction();
        try {
            foreach ($adList as $key => $val) {
                CompanyAdOrder::AddData([
                    'company_id' => $company['id'],
                    'device_uuid' => $device_uuid,
                    'lng' => $lng,
                    'lat' => $lat,
                    'ip' => $ip,
                    'expire_time' => 300,
                    'ad_id' => $val['id'],
                    'show_time' => $val['show_time'],
                    'type' => $val['type'],
                    'width' => $val['width'],
                    'height' => $val['height'],
                    'url' => $val['url'],
                    'status' => 0
                ]);
            }
            \DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            \DB::rollBack();
            return ['code' => 500, 'message' => '获取广告失败，请稍后再试！'];
        }

        $AdOrderList = CompanyAdOrder::getList(['company_id' => $company['id'], 'device_uuid' => $device_uuid, 'status' => 0]);
        foreach ($AdOrderList as $key => $val) {
            $adInfo[] = [
                'ad_id' => $val['ad_id'],
                'show_time' => $val['show_time'],
                'track_url' => $this->create_track_url($val),
                'width' => $val['width'],
                'height' => $val['height'],
                'expire_time' => $val['expire_time'],
                'type' => $val['type'],
//                'hash' => md5_file(public_path("images/my.png")),
                'hash' => md5_file($val['url']),
                'url' => $val['url']
            ];
        }
        return [
            'code' => 0,
            'message' => 'OK',
            'data' => [
                'appid' => $appid,
                'appkey' => $company['appkey'],
                'sign' => $sign,
                'adinfo' => $adInfo
            ]
        ];
    }

    // 创建track_url
    public function create_track_url($CompanyAdOrder)
    {
        $track_id = sha1($CompanyAdOrder['id']);
        // 利用Redis缓存上报的订单信息
        $this->redis->setex($track_id, 1800, json_encode($CompanyAdOrder));
        // 生成上报的地址
        $track_url = config('app.url') . "/open/ads/track/{$track_id}.vue";
        return $track_url;
    }


    // 上报地址，广告播放完成了，就上报该地址，这时候就可以处理广告了
    public function track_url(Request $request, $track_id)
    {
        $data = $this->redis->get($track_id);
        if (empty($data)) {
            return ['code' => 500, 'message' => 'track-url已失效'];
        }
        $data = json_decode($data, true);

        $status = CompanyAdOrder::getValue(['id' => $data['id']], 'status');
        if ($status === 0) {
            CompanyAdOrder::EditData(['id' => $data['id']], ['status' => 1]);
            // 记录上报日志
            $data['adorder_id'] = $data['id'];
            unset($data['id']);
            unset($data['status']);
            unset($data['created_at']);
            unset($data['updated_at']);
            unset($data['deleted_at']);
            CompanyAdOrderTrackLog::AddData($data);
        } else {
            return ['code' => 500, 'message' => 'track-url已失效'];
        }
        return ['code' => 200, 'message' => '上报成功'];
    }
}
