<?php

namespace App\Http\Controllers\Open;

use App\Models\Advert;
use App\Models\MerchantAdOrder;
use App\Http\Controllers\Controller;
use App\Models\MerchantAdvert;
use App\Models\MerchantDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MerchantAdvertController extends Controller
{
    /**
     * 广告市场列表
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/12 0012
     * @Time：10:02
     */
    public function advert_market(Request $request)
    {
        $advert_market = Advert::getPaginate(['status' => 1], [], 10, 'id', 'DESC');
        return ['code' => 200, 'message' => 'ok', 'data' => $advert_market];
    }

    /**
     * 添加广告对应绑定的设备
     * @param Request $request
     * @return array
     * @throws \Exception
     * @author iszmxw <mail@54zm.com>
     * @Date 2019/10/12 0012
     * @Time：10:00
     */
    public function add_merchant_advert(Request $request)
    {
        $data     = $request->all();
        $merchant = $request->get('merchant');
        if (empty($data)) {
            return ['code' => 500, 'message' => '请选择广告和设备！'];
        }
        if (!is_array($data)) {
            return ['code' => 500, 'message' => '数据传输错误，请稍后再试！'];
        }
        // 开启事务回滚
        DB::beginTransaction();
        try {
            foreach ($data as $key => $val) {
                // 检测添加的是全部设备还是一些设备
                if ($val['device_id'] === 'all') {
                    // 添加全部设备
                    $new_data = MerchantDevice::getPluck(['merchant_id' => $merchant['id'], 'status' => 1], 'id');
                } else {
                    // 添加一些设备
                    $new_data = $val['device_id'];
                }
                $old_data = MerchantAdvert::getOne(['merchant_id' => $merchant['id'], 'advert_id' => $val['id']], ['id', 'device_id']);
                if ($old_data) {
                    MerchantAdvert::EditData(['id' => $old_data['id']], [
                        'user_id'     => $val['user_id'],
                        'advert_id'   => $val['id'],
                        'merchant_id' => $merchant['id'],
                        'device_id'   => self::device_id($old_data['device_id'], $new_data),
                        'name'        => $val['name'],
                        'desc'        => $val['desc'],
                        'width'       => $val['width'],
                        'height'      => $val['height'],
                        'url'         => $val['url'],
                        'price'       => $val['price'],
                        'type'        => $val['type'],
                        'city'        => $val['city'],
                        'scene'        => $val['scene'],
                        'status'      => $val['status'],
                    ]);
                } else {
                    MerchantAdvert::AddData([
                        'user_id'     => $val['user_id'],
                        'advert_id'   => $val['id'],
                        'merchant_id' => $merchant['id'],
                        'device_id'   => implode(",", $val['device_id']) . ',',
                        'name'        => $val['name'],
                        'desc'        => $val['desc'],
                        'width'       => $val['width'],
                        'height'      => $val['height'],
                        'url'         => $val['url'],
                        'price'       => $val['price'],
                        'type'        => $val['type'],
                        'city'        => $val['city'],
                        'scene'        => $val['scene'],
                        'status'      => $val['status'],
                    ]);
                }
            }
            DB::commit();
            return ['code' => 200, 'message' => '保存成功'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::debug($e);
            return ['code' => 500, 'message' => '操作失败，请稍后再试！'];
        }
    }

    /**
     * 编辑广告并且添加绑定单个设备
     * @param Request $request
     * @return array
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/12 0012
     * @Time：10:11
     */
    public function advert_device_add(Request $request)
    {
        $id          = $request->get('id');
        $device_uuid = $request->get('device_uuid');
        $merchant    = $request->get('merchant');
        // 根据当前添加的设备uuid查找设备在系统中所属的id
        $device_id = MerchantDevice::getValue(['merchant_id' => $merchant['id'], 'device_uuid' => $device_uuid], 'id');
        // 查询广告原来绑定的设备id集合
        $device_ids = MerchantAdvert::getValue(['id' => $id], 'device_id');
        if (empty($device_ids)) {
            $device_ids = [];
        } else {
            $device_ids = explode(',', $device_ids);
        }
        $res = array_search($device_id, $device_ids);
        if ($res !== false) {
            return ['code' => 500, 'message' => '该设备已经绑定了，请不要重复操作！'];
        } else {
            // 将新增加的添加进数组
            array_push($device_ids, $device_id);
            // 数组去重保留唯一值
            $device_ids = array_unique($device_ids);
            // 数组重新索引
            $device_ids = array_values($device_ids);
            // 最后的结果
            $device_ids = implode(',', $device_ids) . ',';
        }
        // 数据修改操作，增加事务
        DB::beginTransaction();
        try {
            MerchantAdvert::EditData(['id' => $id], [
                'device_id' => $device_ids
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code' => 500, 'message' => '操作失败，稍后再试！'];
        }
        return ['code' => 200, 'message' => '操作成功！'];
    }

    /**
     * 获取单个广告稿当前绑定的所有设备
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/12 0012
     * @Time：10:11
     */
    public function get_advert_bind(Request $request)
    {
        $id               = $request->get('id');
        $advert_bind_list = MerchantAdvert::getOne(['id' => $id]);
        $device_uuid      = MerchantDevice::whereIn('id', explode(',', $advert_bind_list['device_id']))->pluck('device_uuid');
        return ['code' => 200, 'message' => 'ok', 'data' => ['merchant_advert_id' => $id, 'device_list' => $device_uuid]];
    }


    /**
     * 从广告中解绑删除单个设备
     * @param Request $request
     * @return array
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/12 0012
     * @Time：10:11
     */
    public function advert_device_delete(Request $request)
    {
        $id          = $request->get('id');
        $device_uuid = $request->get('device_uuid');
        $merchant    = $request->get('merchant');
        $device_ids  = MerchantAdvert::getValue(['id' => $id], 'device_id');
        $device_ids  = explode(',', $device_ids);
        $device_id   = MerchantDevice::getValue(['merchant_id' => $merchant['id'], 'device_uuid' => $device_uuid], 'id');
        if (empty($device_id)) {
            Log::debug('数据传输错误，对不起您的设备信息不存在，请您确认后再试');
            return ['code' => 500, 'message' => '数据传输错误，对不起您的设备信息不存在，请您确认后再试'];
        }
        // 利用两个数组的差集得到结果
        $device_ids = array_diff($device_ids, [$device_id]);
        // 删除后的剩余设备结果
        $device_ids = implode(',', $device_ids) . ',';
        DB::beginTransaction();
        try {
            MerchantAdvert::EditData(['id' => $id], ['device_id' => $device_ids]);
            DB::commit();
        } catch (\Exception $e) {
            Log::debug($e);
            DB::rollBack();
            return ['code' => 500, 'message' => '操作失败，请稍后再试'];
        }
        return ['code' => 200, 'message' => '操作成功'];
    }


    /**
     * 获取广告对应绑定记录列表
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/12 0012
     * @Time：10:11
     */
    public function advert_bind_list(Request $request)
    {
        $advert_bind_list = MerchantAdvert::getPaginate([], [], 10, 'created_at', 'DESC')->toArray();
        foreach ($advert_bind_list['data'] as $key => $val) {
            if (empty($val['device_id'])) {
                $advert_bind_list['data'][$key]['device_uuid_count'] = 0;
            } else {
                $advert_bind_list['data'][$key]['device_uuid_count'] = count(explode(',', $val['device_id']));
            }
        }
        return ['code' => 200, 'message' => 'ok', 'data' => $advert_bind_list];
    }

    /**
     * 返回新的数据
     * @param $old_data
     * @param $new_data
     * @return string
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/12 0012
     * @Time：10:02
     */
    public static function device_id($old_data, $new_data)
    {
        $old_data = explode(',', $old_data);
        // 合并数组
        $arr = array_merge($old_data, $new_data);
        // 数组去重保留唯一值
        $arr = array_unique($arr);
        // 数组重新索引
        $arr = array_values($arr);
        // 返回字符串
        return implode(',', $arr) . ',';
    }

    /**
     * 获取广告记录列表
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/12 0012
     * @Time：10:10
     */
    public function advert_log(Request $request)
    {
        $merchant   = $request->get('merchant');
        $limit      = $request->get('limit');
        $limit      = $limit ? $limit : 10;
        $advert_log = MerchantAdOrder::getPaginate(['merchant_id' => $merchant['id']], [], $limit, 'id', 'DESC');
        return ['code' => 200, 'message' => 'success', 'data' => $advert_log];
    }
}
