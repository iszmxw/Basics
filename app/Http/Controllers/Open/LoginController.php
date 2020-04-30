<?php

namespace App\Http\Controllers\Open;

use App\Models\LoginLog;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Iszmxw\IpAddress\Address;

class LoginController extends Controller
{
    /**
     * 登录
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:16
     */
    public function login(Request $request)
    {
        $account  = $request->get('username');
        $password = $request->get('password');
        $ip       = $request->ip();
        $address  = Address::address($ip);
        if (empty($account)) {
            return ['code' => 500, 'message' => '请输入登录账号'];
        }
        if (empty($password)) {
            return ['code' => 500, 'message' => '请输入登录密码'];
        }
        $merchant = Merchant::getOne(['account' => $account]);
        if (empty($merchant)) {
            return ['code' => 500, 'message' => '账号不正确，请您确认后再试！'];
        }
        if ($password !== decrypt($merchant['password'])) {
            return ['code' => 500, 'message' => '密码不正确，请你确认后再试！'];
        }
        try {
            $token = md5(rand(10000, 9999) . time() . rand(10000, 9999));
            Cache::put($token, $merchant, 120);
            LoginLog::AddData([
                'account_id' => $merchant['id'],
                'type'       => 2,
                'account'    => $merchant['account'],
                'role'       => '合作商户',
                'ip'         => $ip,
                'address'    => $address['location'],
            ]);
            return ['code' => 200, 'message' => '登录成功', 'data' => ['token' => $token]];
        } catch (\Exception $e) {
            \Log::debug($e);
            return ['code' => 500, 'message' => '系统错误，请稍后再试！'];
        }
    }


    /**
     * 退出登录
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:17
     */
    public function logout(Request $request)
    {
        $token = $request->get('token');
        Cache::forget($token);
        return ['code' => 200, 'message' => '成功', 'data' => 'success'];
    }

}
