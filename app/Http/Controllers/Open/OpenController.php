<?php

namespace App\Http\Controllers\Open;

use App\Http\Requests\Open\GetVideo;
use App\Models\MerchantAdOrder;
use App\Http\Controllers\Controller;
use App\Models\MerchantAdOrderTrackLog;
use App\Models\MerchantAdvert;
use App\Models\MerchantDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class OpenController extends Controller
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
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:27
     */
    public function get_advert(GetVideo $request)
    {
        $sign        = $request->get('sign');           // 本次请求的签名
        $appid       = $request->get('appid');          // 合作商的appid
        $company     = $request->get('company');        // 合作商的信息包含id和appid
        $merchant_id = $company['id'];                      // 合作商id
        $device_id   = $company['device_id'];               // 设备id
        $device_uuid = $request->get('device_uuid');    // 合作商的设备uuid
        $ad_num      = $request->get('ad_num') ?: 12;   // 默认获取12条数据
        $lng         = $request->get('lng');            // 经度
        $lat         = $request->get('lat');            // 纬度
        $ip          = $request->ip();                      // ip
        $adInfo      = [];
        \DB::beginTransaction();
        try {
            $device_scene = MerchantDevice::getValue(['id' => $device_id], 'scene_id');
            // 根据合作商的设备条件获取广告
            $adList      = MerchantAdvert::getList([['status', 1], ['merchant_id', $merchant_id], ['device_id', 'like', '%' . $device_id . ',' . '%']], [], 0, $ad_num);
            $AdOrderList = [];
            foreach ($adList as $key => $val) {
                //  检测该广告播放的场景，判断设备是否符合播放场景
                if ($val['scene'] == 0) {
                    //  创建订单后返回订单信息，然后存入数组列表
                    $AdOrderList[] = MerchantAdOrder::AddData([
                        'merchant_id' => $merchant_id,
                        'device_uuid' => $device_uuid,
                        'lng'         => $lng,
                        'lat'         => $lat,
                        'ip'          => $ip,
                        'expire_time' => 300,
                        'ad_id'       => $val['id'],
                        'type'        => $val['type'],
                        'width'       => $val['width'],
                        'height'      => $val['height'],
                        'url'         => config('iszmxw.OSS_CNAME') . trim($val['url']),
                        'status'      => 0
                    ]);
                } else {
                    //  检测该广告播放的场景，判断设备是否符合播放场景
                    if (in_array($device_scene, explode(',', $val['scene']))) {
                        //  创建订单后返回订单信息，然后存入数组列表
                        $AdOrderList[] = MerchantAdOrder::AddData([
                            'merchant_id' => $merchant_id,
                            'device_uuid' => $device_uuid,
                            'lng'         => $lng,
                            'lat'         => $lat,
                            'ip'          => $ip,
                            'expire_time' => 300,
                            'ad_id'       => $val['id'],
                            'type'        => $val['type'],
                            'width'       => $val['width'],
                            'height'      => $val['height'],
                            'url'         => config('iszmxw.OSS_CNAME') . trim($val['url']),
                            'status'      => 0
                        ]);
                    }
                }
            }
            \DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            \DB::rollBack();
            return ['code' => 500, 'message' => '获取广告失败，请稍后再试！'];
        }
        foreach ($AdOrderList as $key => $val) {
            $adInfo[] = [
                'ad_id'       => $val['ad_id'],
                'track_url'   => $this->create_track_url($val),
                'width'       => $val['width'],
                'height'      => $val['height'],
                'expire_time' => $val['expire_time'],
                'type'        => $val['type'],
                'hash'        => md5_file($val['url']),
                'url'         => $val['url']
            ];
        }
        return [
            'code'    => 200,
            'message' => 'OK',
            'data'    => [
                'appid'  => $appid,
                'appkey' => $company['appkey'],
                'sign'   => $sign,
                'adinfo' => $adInfo
            ]
        ];
    }

    /**
     * 创建track_url
     * @param $CompanyAdOrder
     * @return string
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/12 0012
     * @Time：11:14
     */
    public function create_track_url($CompanyAdOrder)
    {
        $order_id        = $CompanyAdOrder['id'];
        $track_id        = sha1($order_id);
        $time            = 30;
        $order_prefix_id = 'order_' . $order_id;// 因为一个项目中可能会有很多使用到setex的地方，所以给订单id加个前缀
        // 生成订单，并且记录id
        $this->redis->setex($order_prefix_id, $time, $order_id);
        // 利用Redis缓存上报的订单信息
        $this->redis->setex($track_id, $time, json_encode($CompanyAdOrder));
        // 生成上报的地址
        $track_url = config('app.url') . "/api/open/develop/track/{$track_id}.vue";
        return $track_url;
    }


    /**
     * 上报地址，广告播放完成了，就上报该地址，这时候就可以处理广告了
     * @param Request $request
     * @param $track_id
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/12 0012
     * @Time：12:00
     */
    public function track_url(Request $request, $track_id)
    {
        $data = $this->redis->get($track_id);
        if (empty($data)) {
            return ['code' => 500, 'message' => 'track-url已失效'];
        }
        $data = json_decode($data, true);

        $status = MerchantAdOrder::getValue(['id' => $data['id']], 'status');
        if ($status === 0) {
            MerchantAdOrder::EditData(['id' => $data['id']], ['status' => 1]);
            // 记录上报日志
            $data['adorder_id'] = $data['id'];
            $data['scene_id']   = MerchantDevice::getValue(['device_uuid' => $data['device_uuid']], 'scene_id');
            unset($data['id']);
            unset($data['status']);
            unset($data['created_at']);
            unset($data['updated_at']);
            unset($data['deleted_at']);
            MerchantAdOrderTrackLog::AddData($data);
        } else {
            return ['code' => 500, 'message' => 'track-url已失效'];
        }
        return ['code' => 200, 'message' => '上报成功'];
    }
}
