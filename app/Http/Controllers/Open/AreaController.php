<?php

namespace App\Http\Controllers\Open;

use App\Models\Area;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AreaController extends Controller
{
    /**
     * 获取省份
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/12 0012
     * @Time：16:49
     */
    public function get_province(Request $request)
    {
        $data = Area::getList(['level' => 1], ['id', 'code', 'area_name'], 0, 0, 'id', 'ASC');
        return ['code' => 200, 'message' => 'success', 'data' => $data];
    }

    /**
     * 获取城市
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/12 0012
     * @Time：16:49
     */
    public function get_city(Request $request)
    {
        $province_id = $request->get('province_id');
        if ($province_id) {
            $data = Area::getList(['pid' => $province_id], ['id', 'code', 'area_name'], 0, 0, 'id', 'ASC');
        } else {
            $data = Area::getList(['level' => 2], ['id', 'code', 'area_name'], 0, 0, 'id', 'ASC');
        }
        return ['code' => 200, 'message' => 'success', 'data' => $data];
    }

    /**
     * 获取区域
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/12 0012
     * @Time：16:49
     */
    public function get_area(Request $request)
    {
        $city_id = $request->get('city_id');
        if ($city_id) {
            $data = Area::getList(['pid' => $city_id], ['id', 'code', 'area_name'], 0, 0, 'id', 'ASC');
        } else {
            $data = Area::getList(['level' => 3], ['id', 'code', 'area_name'], 0, 0, 'id', 'ASC');
        }
        return ['code' => 200, 'message' => 'success', 'data' => $data];
    }
}
