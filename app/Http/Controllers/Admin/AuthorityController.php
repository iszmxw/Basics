<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\Logs;
use App\Models\Role;
use App\Models\RoleRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthorityController extends Controller
{

    /**
     * 角色列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:03
     */
    public function role_list(Request $request)
    {
        $data['role_list'] = Role::getPaginate([], [], 10, 'id', 'DESC');
        return view('admin.system.role.role_list', $data);
    }


    /**
     * 树形结构数据
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:03
     */
    public function tree_data(Request $request)
    {
        $id         = $request->get('role_id');
        $routes     = Role::getValue(['id' => $id], 'routes');
        $routes     = explode(',', $routes);
        $route_data = RoleRoute::getList([], ['id', 'name as text', 'parent_id as parent', 'depth'], 0, 0, 'id', 'ASC');
        if (empty($id)) {
            //只获取路由权限不设置选中状态
            foreach ($route_data as $key => $val) {
                if ($val['parent'] == 0) $route_data[$key]['parent'] = '#';
                $route_data[$key]['state']['opened']   = false;
                $route_data[$key]['state']['selected'] = false;
            }
        } else {
            // 获取路由并且设置选中状态，在返回
            foreach ($route_data as $key => $val) {
                if ($val['parent'] == 0) $route_data[$key]['parent'] = '#';
                $route_data[$key]['state']['opened']   = false;
                $route_data[$key]['state']['selected'] = false;
                // 检测路由是否为选中状态，由此设置选中状态
                if (in_array($val['id'], $routes)) {
                    // 如果是第一层深度的路由可以，需要路由为可以访问的连接才可以设置selected为true
                    if ($val['depth'] === 1) {
                        $role_info = RoleRoute::getOne(['id' => $val['id']]);
                        if ($role_info['is_menu'] === 1 && $role_info['parent_id'] === 0 && !empty($role_info['route']) && !empty($role_info['icon'])) {
                            $route_data[$key]['state']['selected'] = true;
                        }
                    }
                    // 如果是第二层深度的路由，并且该路由没有子路由可以设置selected为true
                    if ($val['depth'] === 2) {
                        $role_info = RoleRoute::getOne(['parent_id' => $val['id']]);
                        if (!$role_info) {
                            $route_data[$key]['state']['selected'] = true;
                        }
                    }
                    // 如果是第三层深度的路由可以设置selected为true
                    if ($val['depth'] === 3) {
                        $route_data[$key]['state']['selected'] = true;
                    }
                }
            }
        }
        return ['code' => 200, 'message' => '获取数据成功', 'data' => $route_data];
    }


    /**
     * 获取添加角色模态框
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:03
     */
    public function role_modal_add(Request $request)
    {
        return view('admin.system.role.role_modal_add');
    }

    /**
     * 获取角色编辑模态框
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:04
     */
    public function role_modal_edit(Request $request)
    {
        $id = $request->get('id');
        if (empty($id)) return ['code' => 500, 'message' => '数据传输错误！'];
        $data['role'] = Role::getOne(['id' => $id]);
        return view('admin.system.role.role_modal_edit', $data);
    }


    /**
     * 添加角色
     * @param Request $request
     * @return array
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:04
     */
    public function role_add(Request $request)
    {
        $admin_data = $request->get('admin_data');
        $name       = $request->get('name');
        $desc       = $request->get('desc');
        $routes     = $request->get('routes');
        if (empty($name)) return ['code' => 500, 'message' => '请填写角色名称'];
        if (empty($desc)) return ['code' => 500, 'message' => '请填写角色描述'];
        if (count($routes) == 0) return ['code' => 500, 'message' => '请选择权限节点！'];
        $routes = implode(',', $routes);
        DB::beginTransaction();
        try {
            $res = Role::AddData([
                'name'   => $name,
                'desc'   => $desc,
                'routes' => $routes,
            ]);
            Logs::Operation(1, $admin_data['id'], "创建了一个角色，角色的信息如下" . json_encode($res));
            DB::commit();
            return ['code' => 200, 'message' => '创建成功！'];
        } catch (\Exception $e) {
            \Log::debug($e);
            DB::rollBack();
            return ['code' => 500, 'message' => '创建失败，请稍后再试！'];
        }
    }


    /**
     * 删除角色
     * @param Request $request
     * @return array
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:04
     */
    public function role_delete(Request $request)
    {
        $admin_data = $request->get('admin_data');
        $id         = $request->get('id');
        DB::beginTransaction();
        try {
            $res = Role::selected_delete(['id' => $id]);
            Logs::Operation(1, $admin_data['id'], "删除了一个角色，角色的id为【{$id}】，请检查一下该角色下是否有账号在使用");
            DB::commit();
            if ($res) {
                return ['code' => 200, 'message' => '成功删除角色，请检查一下该角色下是否有账号在使用'];
            } else {
                return ['code' => 500, 'message' => '删除失败，请稍后再试！'];
            }
        } catch (\Exception $e) {
            \Log::debug($e);
            DB::rollBack();
            return ['code' => 500, 'message' => '删除失败，请稍后再试！'];
        }
    }


    /**
     * 编辑角色
     * @param Request $request
     * @return array
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:04
     */
    public function role_edit(Request $request)
    {
        $admin_data = $request->get('admin_data');
        $role_id    = $request->get('role_id');
        $name       = $request->get('name');
        $desc       = $request->get('desc');
        $routes     = $request->get('routes');
        if (empty($name)) return ['code' => 500, 'message' => '请填写角色名称'];
        if (empty($desc)) return ['code' => 500, 'message' => '请填写角色描述'];
        if (count($routes) == 0) return ['code' => 500, 'message' => '请选择权限节点！'];
        $routes = implode(',', $routes);
        DB::beginTransaction();
        try {
            $res = Role::EditData(['id' => $role_id], [
                'name'   => $name,
                'desc'   => $desc,
                'routes' => $routes,
            ]);
            Logs::Operation(1, $admin_data['id'], "编辑了一个角色，角色的信息如下" . json_encode($res));
            DB::commit();
            return ['code' => 200, 'message' => '编辑修改成功！'];
        } catch (\Exception $e) {
            \Log::debug($e);
            DB::rollBack();
            return ['code' => 500, 'message' => '编辑失败，请稍后再试！'];
        }
    }
}
