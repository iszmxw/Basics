<?php

namespace App\Http\Controllers\ShiWanFen;

use App\Models\Area;
use App\Models\MerchantAdOrderTrackLog;
use App\Models\MerchantDevice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MigrateController extends Controller
{
    /**
     * 获取十万粉设备信息
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：10:48
     */
    public function get_device(Request $request)
    {
        $sql = <<<SQL
SELECT 
	device.machine_code as device_uuid, 
	device.address, 
	device.lat, 
	device.lng, 
	type.type as scene_name, 
	province.name AS province_name, 
	city.name AS city_name, 
	area.name AS area_name 
FROM 
	`dlc_facility` AS device 
	LEFT JOIN `dlc_shop_set` AS shop ON device.shop_id = shop.id 
	LEFT JOIN `dlc_shop_type` AS type ON shop.type_id = type.id 
	LEFT JOIN `dlc_province` AS province ON province.province_id = shop.province_id 
	LEFT JOIN `dlc_city` AS city ON city.city_id = shop.city_id 
	LEFT JOIN `dlc_area` AS area ON area.area_id = shop.area_id ;
SQL;
        try {
            $res = DB::connection('swf')->select($sql);

            $path = 'import/json/iszmxw/';

            $filename = 'data.json';

            if (!is_dir($path)) {
                $flag = mkdir($path, 0777, true);
            }
            file_put_contents($path . $filename, json_encode($res, true));
            return $res;
        } catch (\Exception $e) {
            return ['code' => '500', 'message' => '获取设备信息失败!具体错误如下！', 'data' => json_encode($e, true)];
        }
    }


    /**
     * 导入数据
     * @param Request $request
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：10:48
     */
    public function import_data(Request $request)
    {
        // $data        = file_get_contents('import/json/iszmxw/data.json');
        // $data        = json_decode($data, true);
        // $merchant_id = 1;
        // foreach ($data as $key => $val) {
        //     DB::beginTransaction();
        //     try {
        //         $where = ['merchant_id' => $merchant_id, 'device_uuid' => $val['device_uuid']];
        //         if (MerchantDevice::checkRowExists($where)) {
        //             $Editres = MerchantDevice::EditData($where, [
        //                 'merchant_id' => $merchant_id,
        //                 'device_uuid' => $val['device_uuid'],
        //                 'province_id' => self::getAreaId($val['province_name'], 1),
        //                 'city_id'     => self::getAreaId($val['city_name'], 2),
        //                 'area_id'     => self::getAreaId($val['area_name'], 3),
        //                 'address'     => $val['address'],
        //                 'scene_id'    => self::getScene($val['scene_name']),
        //                 'lng'         => $val['lng'],
        //                 'lat'         => $val['lat'],
        //             ]);
        //             dump("编辑成功" . $Editres['id']);
        //         } else {
        //             $Addres = MerchantDevice::AddData([
        //                 'merchant_id' => 1,
        //                 'device_uuid' => $val['device_uuid'],
        //                 'province_id' => self::getAreaId($val['province_name'], 1),
        //                 'city_id'     => self::getAreaId($val['city_name'], 2),
        //                 'area_id'     => self::getAreaId($val['area_name'], 3),
        //                 'address'     => $val['address'],
        //                 'scene_id'    => self::getScene($val['scene_name']),
        //                 'lng'         => $val['lng'],
        //                 'lat'         => $val['lat'],
        //                 'status'      => 1,
        //             ]);
        //             dump("添加成功" . $Addres['id']);
        //         }
        //         DB::commit();
        //     } catch (\Exception $e) {
        //         dump($e);
        //         DB::rollBack();
        //     }
        // }
        // exit('导入数据成功');
    }


    /**
     * 获取区域信息
     * @param $name
     * @param $level
     * @return mixed
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：10:48
     */
    public static function getAreaId($name, $level)
    {
        $id = Area::getValue([['area_name', 'like', '%' . $name . '%'], ['level', $level]], 'id');
        return $id;
    }


    /**
     * 获取场景id
     * @param $name
     * @return int
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：11:17
     */
    public static function getScene($name)
    {
        switch ($name) {
            case '通用':
                return 1;
                break;
            case '娱乐休闲':
                return 2;
                break;
            case '商务办公':
                return 3;
                break;
            case '医疗机构':
                return 4;
                break;
            case '餐饮购物':
                return 5;
                break;
            case '社区住宅':
                return 6;
                break;
            case '教育文化':
                return 7;
                break;
            case '交通出行':
                return 8;
                break;
            case '综合场景':
                return 9;
                break;
            default:
                return 1;
                break;
        }
    }


}
