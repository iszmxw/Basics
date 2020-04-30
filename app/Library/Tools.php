<?php


namespace App\Library;


use App\Models\Role;
use App\Models\RoleRoute;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Tools
{
    /**
     * 通过用户信息获取相应的菜单节点
     * @param $admin_data
     * @return bool
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:30
     */
    public static function system_menu($admin_data)
    {
        // 如果是超级管理员或者权限角色ID为1的用户，放开所有菜单
        if ($admin_data['id'] == 1 || $admin_data['role_id'] == 1) {
            $system_menu = RoleRoute::getList(['is_menu' => 1, 'parent_id' => 0], [], 0, 0, 'order', 'ASC');
            foreach ($system_menu as $key => $val) {
                $system_menu[$key]['menu_sub'] = RoleRoute::getList(['is_menu' => 1, 'parent_id' => $val['id']], [], 0, 0, 'order', 'ASC');
            }
            return $system_menu;
        } else {
            $routes      = Role::getValue(['id' => $admin_data['role_id']], 'routes');
            $routes      = explode(',', $routes);
            $system_menu = RoleRoute::whereIn('id', $routes)->where(['is_menu' => 1, 'depth' => 1, 'parent_id' => 0])->orderBy('order', 'ASC')->get()->toArray();
            foreach ($system_menu as $key => $val) {
                $system_menu[$key]['menu_sub'] = RoleRoute::whereIn('id', $routes)->where(['is_menu' => 1, 'depth' => 2, 'parent_id' => $val['id']])->orderBy('order', 'ASC')->get()->toArray();
            }
            return $system_menu;
        }
    }


    /**
     * 处理菜单class样式
     * @param $data
     * @param $url
     * @param bool $sub
     * @return string
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:30
     */
    public function li_class($data, $url, $sub = false)
    {
        $class = '';
        if ($sub) {
            if ($url == $data['route']) {
                $class = 'class="active"';
            }
        } else {
            if (count($data['menu_sub']) > 0) {
                foreach ($data['menu_sub'] as $key => $val) {
                    if ($url == $val['route']) {
                        $class = 'class="active open"';
                    }
                }
            } else {
                if ($url == $data['route']) {
                    $class = 'class="active"';
                }
            }
        }

        return $class;
    }


    /**
     * 处理菜单深度
     * @param $parent_id
     * @return int
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:30
     */
    public static function depth($parent_id)
    {
        if ($parent_id == 0) {
            $depth = 1;
        } else {
            $parent_id = RoleRoute::getValue(['id' => $parent_id], 'parent_id');
            if ($parent_id == 0) {
                $depth = 2;
            } else {
                $depth = 3;
            }
        }
        return $depth;
    }

    /**
     * 判断是否是移动端访问
     * @return bool
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:31
     */
    public static function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return TRUE;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])) {
            return stristr($_SERVER['HTTP_VIA'], "wap") ? TRUE : FALSE;// 找不到为flase,否则为TRUE
        }
        // 判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array(
                'mobile',
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return TRUE;
            }
        }
        if (isset ($_SERVER['HTTP_ACCEPT'])) { // 协议法，因为有可能不准确，放到最后判断
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== FALSE) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === FALSE || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return TRUE;
            }
        }
        return FALSE;
    }


    /**
     * 获取阿里云对象存储图片的详细信息
     * @param $file_path
     * @return bool|mixed
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:31
     */
    public static function getFileInfo($file_path)
    {
        $client = new Client();
        $url    = config('iszmxw.OSS_CNAME') . $file_path . "?x-oss-process=image/info";
        $res    = $client->get($url)->getBody()->getContents();
        if ($res) {
            return json_decode($res, true);
        } else {
            return false;
        }
    }
}