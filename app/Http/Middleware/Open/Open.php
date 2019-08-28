<?php

namespace App\Http\Middleware\Open;

use App\Models\Company;
use App\Models\CompanyDevice;
use Closure;

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
        $route = $request->path();
        $arr_route = explode('/', $route);
        switch ($route) {
            case 'open/iszmxw/' . end($arr_route);
            case 'open/ads/track/' . end($arr_route);
                return $next($request);
                break;
            default;
                $res = self::DevelopCheck($request);
                return self::Response($res, $next);
                break;
        }
    }


    // 开发者权限检测
    public static function DevelopCheck($request)
    {
        // 接收粉丝万岁合作商开发者的appid
        $appid = $request->get('appid');
        // 获取开发者传递过来的时间戳
        $timestamp = $request->get('timestamp');
        // 随机字符串
        $nonce = $request->get('nonce');
        // 接收设备的uuid
        $device_uuid = $request->get('device_uuid');
        $get_signature = $request->get('sign');
        if (empty($appid)) {
            return self::ReArray(0, ['code' => 50005, 'message' => '缺少appid']);
        }
        if (empty($device_uuid)) {
            return self::ReArray(0, ['code' => 50005, 'message' => '缺少device_uuid']);
        }
        $company = Company::getOne(['appid' => $appid], ['id', 'appkey']);
        $appkey = $company['appkey'];
        $res = CompanyDevice::getOne(['id' => $company['id'], 'device_uuid' => $device_uuid], ['status']);
        if (empty($res['status'])) {
            return self::ReArray(0, ['code' => 50005, 'message' => '您的设备uuid不存在我们的系统中，请您联系管理员进行核对设备，检查该设备号是否有误']);
        }
        if ($res['status'] == 2) {
            return self::ReArray(0, ['code' => 50005, 'message' => '您的设备状态存在异常，请您联系合作平台']);
        }

        if (empty($appkey)) {
            return self::ReArray(0, ['code' => 50003, 'message' => '对不起您的appkey不正确，请您进入后台重新初始化']);
        } else {
            $data['appkey'] = $appkey;
            $data['timestamp'] = $timestamp;
            $data['nonce'] = $nonce;
            sort($data, SORT_STRING);
            $str = implode($data);
            $signature = md5($str);
            // 签名效验正确
            if ($get_signature == $signature) {
                // 将登录后的用户信息添加到request中
                $request->attributes->add(['company' => $company]);
                return self::ReArray(1, $request);
            } else {
                return self::ReArray(0, ['code' => 50004, 'message' => 'signature校验错误']);
            }
        }
    }


    // 返回数据
    public static function Response($res, Closure $next)
    {
        if ($res['status'] == 1) {
            return $next($res['response']);
        } else {
            return response()->json($res['response']);
        }
    }

    // 中间件返回数据专用
    public static function ReArray($status, $response)
    {
        $arr = [
            'status' => $status,
            'response' => $response
        ];
        return $arr;
    }


    //解决跨域问题
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
