<?php

namespace App\Http\Controllers\Open;

use App\Models\Merchant;
use App\Models\MerchantDevice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class DeviceController extends Controller
{
    /**
     * 获取设备列表
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/12 0012
     * @Time：16:27
     */
    public function device_list(Request $request)
    {
        $merchant    = $request->get('merchant');
        $limit       = $request->get('limit');
        $limit       = $limit ? $limit : 10;
        $device_uuid = $request->get('device_uuid');
        $province_id = $request->get('province_id');
        $city_id     = $request->get('city_id');
        $area_id     = $request->get('area_id');
        $all         = $request->get('all');
        $where       = [['merchant_device.merchant_id', $merchant['id']]];
        // 按照设备UUID搜索
        if ($device_uuid) {
            $where[] = ['merchant_device.device_uuid', $device_uuid];
        }
        // 按照省份搜索
        if ($province_id) {
            $where[] = ['merchant_device.province_id', $province_id];
        }
        // 按照城市搜索
        if ($city_id) {
            $where[] = ['merchant_device.city_id', $city_id];
        }
        // 按照区域搜索
        if ($area_id) {
            $where[] = ['merchant_device.area_id', $area_id];
        }
        // 查询结果
        $device_list = MerchantDevice::where($where)
            // 关联出合作商户信息
            ->leftJoin('merchant', 'merchant.id', '=', 'merchant_device.merchant_id')
            // 关联查出省信息
            ->leftJoin('area as province', 'province.id', '=', 'merchant_device.province_id')
            // 关联查出市信息
            ->leftJoin('area as city', 'city.id', '=', 'merchant_device.city_id')
            // 关联查出区域
            ->leftJoin('area', 'area.id', '=', 'merchant_device.area_id')
            // 关联查询出场景
            ->leftJoin('device_scene', 'device_scene.id', '=', 'merchant_device.scene_id')
            // 选择字段
            ->select([
                'merchant_device.id',
                'merchant_device.device_uuid',
                'merchant_device.address',
                'merchant_device.lng',
                'merchant_device.lat',
                'merchant_device.status',
                'merchant_device.created_at',
                'device_scene.name as scene_name',
                'province.area_name as province',
                'city.area_name as city',
                'area.area_name as area',
                'merchant.company'
            ])
            ->orderBy('merchant_device.id', 'DESC');
        if ($all) {
            // 获取全部数据
            $device_list = $device_list->get();
        } else {
            // 每页取10条数据
            $device_list = $device_list->paginate($limit);
        }
        return ['code' => 200, 'message' => 'success', 'data' => $device_list];
    }


    /**
     * 获取登录合作商的所有设备UUID
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/12 0012
     * @Time：16:27
     */
    public function device_uuid(Request $request)
    {
        $merchant    = $request->get('merchant');
        $device_uuid = MerchantDevice::getList(['merchant_id' => $merchant['id']], ['device_uuid as value']);
        return ['code' => 200, 'message' => 'ok', 'data' => $device_uuid];
    }
}
