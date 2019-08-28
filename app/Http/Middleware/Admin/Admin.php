<?php

namespace App\Http\Middleware\Admin;

use App\Models\Company;
use App\Models\CompanyDevice;
use App\Models\Role;
use App\Models\RoleRoute;
use Closure;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

class Admin
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
            case 'admin/login';
            case 'admin/login_check';
            case 'admin/error_page';
                return $next($request);
            default;
                //所有后台用户检测登录
                $res = self::LoginAndRoleCheck($request);
                return self::Response($res, $next);
                break;
        }
    }


    // 检测用户是否登录
    public static function LoginCheck($request)
    {
        $admin_data = session()->get('admin_data');
        $system_menu = session()->get('system_menu');
        if (!empty($admin_data) && !empty($system_menu)) {
            $route = $request->path();
            $title = RoleRoute::getValue(['route' => $route], 'name');
            View::share('admin_data', $admin_data);
            View::share('system_menu', $system_menu);
            View::share('title', $title);
            $request->attributes->add(['admin_data' => $admin_data]);
            return self::ReArray(1, $request);
        } else {
            return self::ReArray(0, redirect('admin/login'));
        }
    }


    // 检测用户的登录和角色权限
    public static function LoginAndRoleCheck($request)
    {
        $res = self::LoginCheck($request);
        if ($res['status'] == 1) {
            // 如果登录成功检测用户的权限
            return self::RoleCheck($request);
        } else {
            return $res;
        }
    }


    // 用户角色权限检测
    public static function RoleCheck($request)
    {
        $route = $request->path();
        $admin_data = session()->get('admin_data');
        $admin_role = session()->get('admin_role');
        // 查找用户可访问路由
        if (empty($admin_role)) {
            $admin_role = [];
            $role_id = $admin_data['role_id'];
            $routes = Role::getValue(['id' => $role_id], 'routes');
            $routes = explode(',', $routes);
            $routes = RoleRoute::where('route', '<>', null)->whereIn('id', $routes)->get(['route'])->toArray();
            foreach ($routes as $key => $val) {
                $admin_role[] = $val['route'];
            }
            $public_route = config('iszmxw.admin_role.public_route');
            // 计算出用户所有允许访问的路由
            $admin_role = array_merge($admin_role, $public_route);
            // 更新登录信息，同时缓存从新开始，1小时后失效
            session()->put('admin_role', $admin_role);
        } else {
            $admin_role = session()->get('admin_role');
        }
        // 判断角色是否具备当前请求的路由
        if ($admin_data['id'] != 1 && !in_array($route, $admin_role)) {
            if (Request::isMethod('post')) {
                return self::ReArray(0, ['code' => 500, 'message' => '抱歉！您不具备权限！']);
            } else if (Request::isMethod('get')) {
                return self::ReArray(0, redirect('admin/error_page'));
            }
        } else {
            return self::ReArray(1, $request);
        }
    }

    // 返回数据
    public static function Response($res, Closure $next)
    {
        if ($res['status'] == 1) {
            return $next($res['response']);
        } else {
            if (Request::isMethod('post')) {
                return response()->json($res['response']);
            } else if (Request::isMethod('get')) {
                return $res['response'];
            }
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
