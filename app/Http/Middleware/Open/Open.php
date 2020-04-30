<?php

namespace App\Http\Middleware\Open;

use App\Models\Merchant;
use App\Models\MerchantDevice;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Open
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        self::cors();
        $route     = $request->path();
        $arr_route = explode('/', $route);
        switch ($route) {
            case 'api/open/login'; // 登录
            case 'api/open/iszmxw/' . end($arr_route);
            case 'api/open/develop/track/' . end($arr_route);
            case 'api/open/sms/ali_sms';
                return $next($request);
                break;
            case 'api/open/develop/get_advert';
                $res = self::DevelopCheck($request);
                return self::Response($res, $next);
                break;
            default;
                $res = self::LoginCheck($request);
                return self::Response($res, $next);
                break;
        }
    }


    /**
     * 登录检测
     * @param $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:20
     */
    public static function LoginCheck($request)
    {
        // 从头部获取token
        $OpenToken = $request->header('Open-Token');
        // 接收第一次传过来的token
        $token = $request->get('token');
        // token最终结果
        $token    = empty($token) ? $OpenToken : $token;
        $merchant = Cache::get($token);
        if (empty($merchant)) {
            if ($request->getMethod() === 'OPTIONS') {
                return self::ReArray(0, ['code' => 200, 'message' => 'ok']);
            } else {
                return self::ReArray(0, ['code' => 500, 'message' => '登录失效，请您重新登录']);
            }
        } else {
            // 将登录后的用户信息添加到request中
            $request->attributes->add(['merchant' => $merchant]);
            return self::ReArray(1, $request);
        }
    }

    /**
     * 开发者权限检测
     * @param $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:21
     */
    public static function DevelopCheck($request)
    {
        // 接收粉丝万岁合作商开发者的appid
        $appid = $request->get('appid');
        // 获取开发者传递过来的时间戳
        $timestamp = $request->get('timestamp');
        // 随机字符串
        $nonce = $request->get('nonce');
        // 接收设备的uuid
        $device_uuid   = $request->get('device_uuid');
        $get_signature = $request->get('sign');
        if (empty($appid)) {
            return self::ReArray(0, ['code' => 501, 'message' => '缺少appid']);
        }
        if (empty($device_uuid)) {
            return self::ReArray(0, ['code' => 502, 'message' => '缺少device_uuid']);
        }
        $company = Merchant::getOne(['appid' => $appid], ['id', 'appkey']);
        $appkey  = $company['appkey'];
        $res     = MerchantDevice::getOne(['merchant_id' => $company['id'], 'device_uuid' => $device_uuid], ['status', 'id']);

        if (empty($res['status'])) {
            return self::ReArray(0, ['code' => 503, 'message' => '您的设备uuid不存在我们的系统中，请您联系管理员进行核对设备，检查该设备号是否有误']);
        }
        if ($res['status'] == 2) {
            return self::ReArray(0, ['code' => 504, 'message' => '您的设备状态存在异常，请您联系合作平台']);
        }

        if (empty($appkey)) {
            return self::ReArray(0, ['code' => 505, 'message' => '对不起您的appkey不正确，请您进入后台重新初始化']);
        } else {
            $data['appkey']    = $appkey;
            $data['timestamp'] = $timestamp;
            $data['nonce']     = $nonce;
            sort($data, SORT_STRING);
            $str       = implode($data);
            $signature = sha1($str);
            // 签名效验正确
            if ($get_signature == $signature) {
                $company['device_id'] = $res['id'];
                // 将登录后的用户信息添加到request中
                $request->attributes->add(['company' => $company]);
                return self::ReArray(1, $request);
            } else {
                return self::ReArray(0, ['code' => 506, 'message' => 'signature校验错误', 'data' => ['your_signature' => $get_signature, 'system_signature' => $signature]]);
            }
        }
    }


    /**
     * 返回数据
     * @param $res
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:21
     */
    public static function Response($res, Closure $next)
    {
        if ($res['status'] == 1) {
            return $next($res['response']);
        } else {
            return response()->json($res['response']);
        }
    }

    /**
     * 中间件返回数据专用
     * @param $status
     * @param $response
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:21
     */
    public static function ReArray($status, $response)
    {
        $arr = [
            'status'   => $status,
            'response' => $response
        ];
        return $arr;
    }


    /**
     * 解决跨域问题
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:21
     */
    public static function cors()
    {
        // 允许来自任何来源
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // 决定$_SERVER['HTTP_ORIGIN']是否为一个
            // 您希望允许，如果允许：
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // 一天缓存
        }
        // 在选项请求期间接收访问控制头
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }
    }

}
