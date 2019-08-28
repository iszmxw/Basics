<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\Logs;
use App\Models\Admin;
use App\Models\LoginLog;
use App\Models\OperationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    /**
     * 系统首页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dashboard(Request $request)
    {
        $data['login_log'] = LoginLog::getPaginate([], [], 10, 'id');
        $data['operation_log'] = OperationLog::getPaginate();
        return view('admin.dashboard', $data);
    }

    /**
     * 个人中心
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profile(Request $request)
    {
        $admin_data = $request->get('admin_data');
        $data['admin_data'] = $admin_data;
        return view('admin.system.profile', $data);
    }

    /**
     * 修改个人资料
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function profile_edit(Request $request)
    {
        $admin_data = $request->get('admin_data');
        $old_password = $request->get('old_password');
        $new_password = $request->get('new_password');
        $re_password = $request->get('re_password');

        if (empty($new_password)) {
            return ['code' => 500, 'message' => '新密码不能为空！'];
        }
        if ($new_password != $re_password) {
            return ['code' => 500, 'message' => '两次密码输入不一致，请您核对后再试！'];
        }
        if ($old_password != decrypt($admin_data['password'])) {
            return ['code' => 500, 'message' => '原密码不正确，请您确认后再试！'];
        }
        $password = encrypt($new_password);
        // 开启事务
        DB::beginTransaction();
        try {
            Admin::EditData(['id' => $admin_data['id']], ['password' => $password]);
            // 添加操作日志
            Logs::Operation(1, $admin_data['id'], '修改了登录密码！');
            $admin_data['password'] = $password;
            // 更新缓存中的密码
            session()->put('admin_data', $admin_data);
            DB::commit();
            return ['code' => 200, 'message' => "修改成功，请牢记您的新密码【{$new_password}】"];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code' => 500, 'message' => '修改失败，请稍后再试！'];
        }
    }

}
