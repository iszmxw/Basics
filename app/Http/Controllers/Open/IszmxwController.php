<?php

namespace App\Http\Controllers\Open;

use App\Models\MerchantAdvert;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class IszmxwController extends Controller
{
    public $nonce;
    public $timestamp;
    public $appid;
    public $appkey;
    public $device_uuid;
    public $lng;
    public $lat;
    public $ad_num;
    public $url;

    public function __construct()
    {
        $this->appid       = "ad1f5d4bc06f19b";
        $this->timestamp   = time();
        $this->nonce       = null;
        $this->device_uuid = "9BH004092";
        $this->lng         = 121212.12121;
        $this->lat         = 4745154.1584;
        $this->ad_num      = 12;
        $this->appkey      = "fb56be4d66ec21342c9195897f2d6375";
        $this->url         = "http://ad.10wan.ren/api/open/develop/get_advert";
    }


    /**
     * 获取广告
     * @param Request $request
     * @return string
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:16
     */
    public function get_ad(Request $request)
    {
        $client      = new Client();
        $sign        = $this->sign();
        $url         = $this->url;
        $form_params = [
            'appid'       => $this->appid,
            'timestamp'   => $this->timestamp,
            'nonce'       => $this->nonce,
            'sign'        => $sign,
            'device_uuid' => $this->device_uuid,
            'lng'         => $this->lng,
            'lat'         => $this->lat,
            'ad_num'      => $this->ad_num,
        ];
        $res         = $client->post($url, ['form_params' => $form_params])->getBody()->getContents();
        return $res;
    }

    /**
     * 签名方法
     * @return string
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:16
     */
    public function sign()
    {
        $data['appkey']    = $this->appkey;
        $data['timestamp'] = $this->timestamp;
        $data['nonce']     = $this->nonce;
        sort($data, SORT_STRING);
        $str = implode($data);
        return (sha1($str));
    }


    /**
     * 上传文件
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:16
     */
    public function uploads(Request $request)
    {
        $file = $request->file('files');
        if (strpos($file->getMimeType(), "video") !== false) {
            $Folder = "videos";
        } elseif (strpos($file->getMimeType(), "image") !== false) {
            $Folder = "images";
        } else {
            $Folder = "/";
        }
        // 判断图片有效性
        if (!$file->isValid()) {
            return back()->withErrors('上传文件无效..');
        }
        $disk   = Storage::disk('oss');
        $rename = time() . rand(1000, 9999) . "." . $file->getClientOriginalExtension();
        // 上传文件到images目录并且重命名
        $re = $disk->putFileAs($Folder, $file, $rename);
        dump($re);
    }


    public function set_redis(Request $request)
    {
        $redis = Redis::connection('publisher');//创建新的实例
        //这里是接收到用户传来的下单信息，存入数据库后，返回一个订单id
        //我们让返回的订单ID为2019
        $order_id = 2019;
        //因为一个项目中可能会有很多使用到setex的地方，所以给订单id加个前缀
        $order_prefix_id = 'order_' . $order_id;
        //将订单ID存入redis缓存中，并且设置过期时间为5秒
        $key_name      = $order_prefix_id; //我们在订阅中只能接收到$key_name的值
        $expire_second = 5; //设置过期时间，单位为秒
        $value         = $order_id;
        $redis->setex($key_name, $expire_second, $value);
        echo "设置过期key=" . $order_prefix_id . "成功";
    }

}
