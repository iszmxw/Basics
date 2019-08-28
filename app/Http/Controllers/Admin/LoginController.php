<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\IpAddress;
use App\Library\Tools;
use App\Models\Admin;
use App\Models\LoginLog;
use App\Models\Role;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    // 登录系统
    public function login(Request $request)
    {
        $admin_data = session()->get('admin_data');
        if ($admin_data) {
            return redirect('admin/dashboard');
        } else {
            return view('admin.login');
        }
    }

    // 登录检测
    public function login_check(Request $request)
    {
        $ip = $request->ip();
        $address = IpAddress::address($ip);
        $account = $request->get('account');
        $password = $request->get('password');
        if (empty($account)) {
            return ['code' => 500, 'message' => '请输入登录账号'];
        }
        if (empty($password)) {
            return ['code' => 500, 'message' => '请输入登录密码！'];
        }
        $admin_data = Admin::getOne(['account' => $account]);
        if (empty($admin_data)) {
            return ['code' => 500, 'message' => '用户名输入有误，请您核实后再试！'];
        }
        if ($admin_data['status'] != 1) {
            return ['code' => 500, 'message' => '对不起您的账号异常，如有需要，请你联系后台管理人员！'];
        }
        if ($password == decrypt($admin_data['password'])) {
            $role_name = Role::getValue(['id' => $admin_data['role_id']], 'name');
            $admin_data['role_name'] = $role_name;
            $admin_data['ip'] = $ip;
            $admin_data['address'] = $address['location'];
            $system_menu = Tools::system_menu($admin_data);
            // 记录登录日志
            LoginLog::AddData([
                'account_id' => $admin_data['id'],
                'type' => 1,
                'account' => $admin_data['account'],
                'role' => $role_name,
                'ip' => $address['origip'],
                'address' => $address['location'],
            ]);
            session()->put('admin_data', $admin_data);
            session()->put('system_menu', $system_menu);
            return ['code' => 200, 'message' => '恭喜你登录成功！'];
        } else {
            return ['code' => 500, 'message' => '对不起，密码错误！'];
        }
    }

    //非法操作提示页面
    public function error_page(Request $request)
    {
        $msg = $request->get('msg');
        if (empty($msg)) {
            $msg = '对不起，您不具备权限!';
        }
        return view('admin.common.tips', ['msg' => $msg]);
    }


    // 退出系统
    public function quit()
    {
        session()->put('admin_data', '');
        session()->put('menu_data', '');
        session()->flush();
        return redirect('admin/login');
    }
}
