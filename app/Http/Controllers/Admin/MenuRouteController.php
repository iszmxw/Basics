<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RouteAdd;
use App\Library\Logs;
use App\Library\Tools;
use App\Models\RoleRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuRouteController extends Controller
{

    /**
     * 左侧菜单和系统路由管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function menu_route(Request $request)
    {
        $where_route = ['parent_id' => 0];
        $where_menu = ['is_menu' => 1, 'parent_id' => 0];

        $route_data = RoleRoute::getList($where_route, [], 0, 0, 'order', 'ASC');
        foreach ($route_data as $key => $val) {
            $route_data[$key]['menu_sub'] = RoleRoute::getList(['parent_id' => $val['id']], [], 0, 0, 'order', 'ASC');
            foreach ($route_data[$key]['menu_sub'] as $kk => $vv) {
                $route_data[$key]['menu_sub'][$kk]['menu_sub'] = RoleRoute::getList(['parent_id' => $vv['id']], [], 0, 0, 'order', 'ASC');
            }
        }

        $menu_data = RoleRoute::getList($where_menu, [], 0, 0, 'order', 'ASC');
        foreach ($menu_data as $key => $val) {
            $menu_data[$key]['menu_sub'] = RoleRoute::getList(['is_menu' => 1, 'parent_id' => $val['id']], [], 0, 0, 'order', 'ASC');
        }

        // 上级菜单数据
        $data['parent_data'] = RoleRoute::getList([['depth', '<', 3]], ['id', 'name'], 0, 0, 'order', 'ASC');

        $data['route_data'] = $route_data;
        $data['menu_data'] = $menu_data;
        return view('admin.system.menu_route', $data);
    }

    /**
     * 添加菜单
     * @param RouteAdd $request
     * @return array
     * @throws \Exception
     */
    public function route_add(RouteAdd $request)
    {
        $data = $request->except('_token');
        $admin_data = $request->get('admin_data');
        // 根据parent_id处理菜单深度
        $data['depth'] = Tools::depth($data['parent_id']);
        DB::beginTransaction();
        try {
            $res = RoleRoute::AddData($data);
            Logs::Operation(1, $admin_data['id'], "添加了一条路由，路由地址的基本信息如下" . json_encode($res));
            DB::commit();
            // 更新菜单缓存
            $system_menu = Tools::system_menu($admin_data);
            session()->put('system_menu', $system_menu);
            return ['code' => 200, 'message' => '添加成功'];
        } catch (\Exception $e) {
            \Log::debug($e);
            DB::rollBack();
            return ['code' => 500, 'message' => '添加失败，请稍后再试！'];
        }
    }

    /**
     * 删除路由信息
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function route_delete(Request $request)
    {
        $id = $request->get('id');
        $admin_data = $request->get('admin_data');
        if (empty($id)) {
            return ['code' => 500, 'message' => '系统错误，请稍后再试！'];
        }
        DB::beginTransaction();
        try {
            RoleRoute::selected_delete(['id' => $id]);
            Logs::Operation(1, $admin_data['id'], "删除了路由信息，路由的id为【{$id}】");
            DB::commit();
            // 更新菜单缓存
            $system_menu = Tools::system_menu($admin_data);
            session()->put('system_menu', $system_menu);
            return ['code' => 200, 'message' => '删除成功！'];
        } catch (\Exception $e) {
            \Log::debug($e);
            DB::rollBack();
            return ['code' => 500, 'message' => '删除失败，请稍后再试！'];
        }

    }

    /**
     * 编辑路由信息
     * @param RouteAdd $request
     * @return array
     * @throws \Exception
     */
    public function route_edit(RouteAdd $request)
    {
        $id = $request->get('route_id');
        $data = $request->except(['_token', 'route_id']);
        $admin_data = $request->get('admin_data');
        // 根据parent_id处理菜单深度
        $data['depth'] = Tools::depth($data['parent_id']);
        DB::beginTransaction();
        try {
            $res = RoleRoute::EditData(['id' => $id], $data);
            Logs::Operation(1, $admin_data['id'], "编辑了路由信息，信息如下" . json_encode($res));
            DB::commit();
            // 更新菜单缓存
            $system_menu = Tools::system_menu($admin_data);
            session()->put('system_menu', $system_menu);
            return ['code' => 200, 'message' => '编辑成功'];
        } catch (\Exception $e) {
            \Log::debug($e);
            DB::rollBack();
            return ['code' => 500, 'message' => '编辑失败，请稍后再试！'];
        }
    }

    /**
     * 获取路由信息
     * @param Request $request
     * @return array
     */
    public function route_info(Request $request)
    {
        $id = $request->get('id');
        if (empty($id)) {
            return ['code' => 500, 'message' => '系统错误请稍后再试！'];
        }
        $route_info = RoleRoute::getOne(['id' => $id]);
        return ['code' => 200, 'message' => '获取数据成功', 'data' => $route_info];
    }

    /**
     * 菜单排序
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function route_order(Request $request)
    {
        $id = $request->get('id');
        $order = $request->get('order');
        $admin_data = $request->get('admin_data');
        DB::beginTransaction();
        try {
            RoleRoute::EditData(['id' => $id], ['order' => $order]);
            Logs::Operation(1, $admin_data['id'], "编辑了菜单顺序");
            DB::commit();
            return ['code' => 200, 'message' => '编辑成功！'];
        } catch (\Exception $e) {
            \Log::debug($e);
            DB::rollBack();
            return ['code' => 500, 'message' => '编辑失败，请稍后再试！'];
        }
    }

}
