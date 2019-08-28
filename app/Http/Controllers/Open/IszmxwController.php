<?php

namespace App\Http\Controllers\Open;

use App\Models\Company;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use OSS\OssClient;
use OSS\Core\OssException;

class IszmxwController extends Controller
{
    public $nonce;
    public $timestamp;
    public $appid;
    public $appkey;
    public $device_uuid;
    public $type;
    public $lng;
    public $lat;
    public $url;

    public function __construct()
    {
        $this->nonce = null;
        $this->timestamp = time();
        $this->appid = "ad1f5d4bc06f19b";
        $this->appkey = "a9404de85f62c037360e1be873134a00";
        $this->device_uuid = "067888e8f3c6";
        $this->type = 0;
        $this->lng = 121212.12121;
        $this->lat = 4745154.1584;
        $this->url = "http://ad.10wan.ren/open/ads/get";
    }

    //uuid
    public function uuid(Request $request)
    {
        $u4 = Uuid::uuid4()->getNodeHex();

        $app_secert = md5('ad1' . time());
        dump($u4);
        dump($app_secert);
    }


    /**
     * 创建合作商户
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function create_company(Request $request)
    {
        $node = Uuid::uuid4()->getNodeHex();
        $id = Company::getMax() + 1;
        $appid = "ad" . $id . $node;
        $appkey = md5("ad$id" . time());
        $data = [
            'company' => '追梦小窝测试',
            'appid' => $appid,
            'appkey' => $appkey,
        ];
        // 开启事务回滚
        \DB::beginTransaction();
        try {
            $res = Company::AddData($data);
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return ['code' => 500, 'message' => '创建合作商户失败' . $e];
        }
        return ['code' => 200, 'message' => 'ok', 'data' => $res];
    }


    // 获取广告
    public function get_ad(Request $request)
    {
        $client = new Client();
        $sign = $this->sign();
        $url = $this->url;
        $form_params = [
            'appid' => $this->appid,
            'timestamp' => $this->timestamp,
            'nonce' => $this->nonce,
            'sign' => $sign,
            'device_uuid' => $this->device_uuid,
            'lng' => $this->lng,
            'lat' => $this->lat,
            'ad_num' => 12,
        ];
        $res = $client->post($url, ['form_params' => $form_params])->getBody()->getContents();
        return $res;
    }

    // 签名方法
    public function sign()
    {
        $data['appkey'] = $this->appkey;
        $data['timestamp'] = $this->timestamp;
        $data['nonce'] = $this->nonce;
        sort($data, SORT_STRING);
        $str = implode($data);
        return (md5($str));
    }


    // 上传文件
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
        $disk = Storage::disk('oss');
        $rename = time() . rand(1000, 9999) . "." . $file->getClientOriginalExtension();
        // 上传文件到images目录并且重命名
        $re = $disk->putFileAs($Folder, $file, $rename);
        dump($re);
    }

    // 获取文件
    public function get_file(Request $request)
    {
        $disk = Storage::disk('oss');
        $files = "images/15664463423539.mp4";
        $exists = $disk->has($files);
        if ($exists) {
            $url = "http://files.fensiwansui.com/" . $files;
        } else {
            $url = null;
        }
        return $url;

    }


    // 获取所有session
    public function session_all(Request $request)
    {
        $data = session()->all();
        $data1 = $request->session()->all();
        dump($data);
        dump($data1);
    }
}
