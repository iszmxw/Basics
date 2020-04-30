<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\Logs;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    /**
     * 创建系统管理人员
     * @param Request $request
     * @return array
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:00
     */
    public function account_add(Request $request)
    {
        $data       = $request->except('_token');
        $admin_data = $request->get('admin_data');
        if ($data['role_id'] === 0) {
            return ['code' => 500, 'message' => '请选择角色'];
        }
        if (empty($data['account'])) {
            return ['code' => 500, 'message' => '请输入账号'];
        }
        if (empty($data['password'])) {
            return ['code' => 500, 'message' => '请输入密码！'];
        }
        if (Admin::checkRowExists(['account' => $data['account']])) {
            return ['code' => 500, 'message' => '对不起该账号已经存在，请您换个账号！'];
        }
        $data['password'] = encrypt($data['password']);
        // 开启数据库事务
        DB::beginTransaction();
        try {
            $res = Admin::AddData($data);
            Logs::Operation(1, $admin_data['id'], "创建了一个系统管理员，管理人员的信息如下" . json_encode($res));
            DB::commit();
            return ['code' => 200, 'message' => '创建成功！'];
        } catch (\Exception $e) {
            \Log::debug($e);
            DB::rollBack();
            return ['code' => 500, 'message' => '创建失败，请稍后再试！'];
        }
    }


    /**
     * 冻结、解冻账号
     * @param Request $request
     * @return array
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:02
     */
    public function account_status(Request $request)
    {
        $id           = $request->get('id');
        $admin_data   = $request->get('admin_data');
        $admin_status = Admin::getValue(['id' => $id], 'status');
        if ($admin_status === 1) {
            $tips   = "冻结了";
            $status = -1;
        } else {
            $tips   = "解冻了";
            $status = 1;
        }
        DB::beginTransaction();
        try {
            Admin::EditData(['id' => $id], ['status' => $status]);
            Logs::Operation(1, $admin_data['id'], "{$tips}id为【{$id}】的账号");
            DB::commit();
            return ['code' => 200, 'message' => '操作成功！'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code' => 500, 'message' => '操作失败，请稍后再试！'];
        }
    }


    /**
     * 编辑账号
     * @param Request $request
     * @return array
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:02
     */
    public function account_edit(Request $request)
    {
        $admin_id   = $request->get('admin_id');
        $data       = $request->except(['_token', 'admin_id']);
        $admin_data = $request->get('admin_data');
        if ($data['role_id'] === 0) {
            return ['code' => 500, 'message' => '请选择角色'];
        }
        if (empty($data['password'])) {
            return ['code' => 500, 'message' => '请输入密码！'];
        }
        $data['password'] = encrypt($data['password']);
        DB::beginTransaction();
        try {
            $res = Admin::EditData(['id' => $admin_id], $data);
            Logs::Operation(1, $admin_data['id'], "修改了管理员ID为{$admin_id}的密码和角色，请您留意，具体信息如下" . json_encode($res));
            DB::commit();
            return ['code' => 200, 'message' => '编辑成功！'];
        } catch (\Exception $e) {
            \Log::debug($e);
            DB::rollBack();
            return ['code' => 500, 'message' => '编辑失败，请稍后再试！'];
        }
    }


    /**
     * 获取单个管理员信息
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:02
     */
    public function account_info(Request $request)
    {
        $id         = $request->get('id');
        $admin_info = Admin::getOne(['id' => $id], ['id', 'role_id', 'account']);
        return ['code' => 200, 'message' => '获取成功', 'data' => $admin_info];
    }

    /**
     * 系统管理员列表页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:03
     */
    public function account_list(Request $request)
    {
        $data['account_list'] = Admin::getAccountPaginate([], ['admin.*', 'role.name as role_name'], 10, 'id', 'DESC');
        $data['role_list']    = Role::getList([], ['id', 'name']);
        return view('admin.system.account_list', $data);
    }

}
