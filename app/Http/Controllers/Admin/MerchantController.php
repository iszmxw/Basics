<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\Logs;
use App\Models\Merchant;
use App\Models\MerchantDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class MerchantController extends Controller
{
    /**
     * 创建合作商户
     * @param Request $request
     * @return array
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:07
     */
    public function merchant_add(Request $request)
    {
        $data       = $request->except('_token');
        $admin_data = $request->get('admin_data');
        if (empty($data['company'])) {
            return ['code' => 500, 'message' => '请设置商户名称！'];
        }
        if (empty($data['account'])) {
            return ['code' => 500, 'message' => '请设置商户账号'];
        }
        if (empty($data['password'])) {
            return ['code' => 500, 'message' => '请设置商户密码！'];
        }
        if (Merchant::checkRowExists(['account' => $data['account']])) {
            return ['code' => 500, 'message' => '对不起该账号已经存在，请您换个账号！'];
        }
        $data['password'] = encrypt($data['password']);
        $node             = Uuid::uuid4()->getNodeHex();
        $id               = Merchant::getMax() + 1;
        $appid            = "ad" . $id . $node;
        $appkey           = md5("ad$id" . time());
        $data['appid']    = $appid;
        $data['appkey']   = $appkey;
        DB::beginTransaction();
        try {
            $res = Merchant::AddData($data);
            Logs::Operation(1, $admin_data['id'], "创建了一个合作商户，商户的信息如下" . json_encode($res));
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
     * @Time：16:07
     */
    public function merchant_status(Request $request)
    {
        $id              = $request->get('id');
        $admin_data      = $request->get('admin_data');
        $merchant_status = Merchant::getValue(['id' => $id], 'status');
        if ($merchant_status === 1) {
            $tips   = "冻结了";
            $status = -1;
        } else {
            $tips   = "解冻了";
            $status = 1;
        }
        DB::beginTransaction();
        try {
            Merchant::EditData(['id' => $id], ['status' => $status]);
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
     * @Time：16:07
     */
    public function merchant_edit(Request $request)
    {
        $merchant_id = $request->get('merchant_id');
        $password    = $request->get('password');
        $admin_data  = $request->get('admin_data');
        if (empty($password)) {
            return ['code' => 500, 'message' => '请输入密码！'];
        }
        $data['password'] = encrypt($password);
        DB::beginTransaction();
        try {
            Merchant::EditData(['id' => $merchant_id], $data);
            Logs::Operation(1, $admin_data['id'], "修改了合作商户ID为{$merchant_id}的登录密码请您留意");
            DB::commit();
            return ['code' => 200, 'message' => '编辑成功！'];
        } catch (\Exception $e) {
            \Log::debug($e);
            DB::rollBack();
            return ['code' => 500, 'message' => '编辑失败，请稍后再试！'];
        }


    }


    /**
     * 商户列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:07
     */
    public function merchant_list(Request $request)
    {
        $data['merchant_list'] = Merchant::getPaginate();
        return view('admin.merchant.merchant_list', $data);
    }

    /**
     * 获取单合作商户的信息
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:07
     */
    public function merchant_info(Request $request)
    {
        $id            = $request->get('id');
        $merchant_info = Merchant::getOne(['id' => $id], ['id', 'account']);
        return ['code' => 200, 'message' => '获取成功', 'data' => $merchant_info];
    }


    /**
     * 合作商设备列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:08
     */
    public function device_list(Request $request)
    {
        $search_data['device_uuid'] = $request->get('device_uuid');
        $where                      = [];
        if (isset($search_data['device_uuid'])) {
            $where['device_uuid'] = $search_data['device_uuid'];
        }
        $data['device_list']   = MerchantDevice::where($where)
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
            ->orderBy('merchant_device.id', 'DESC')
            // 每页取10条数据
            ->paginate(10);
        $data['merchant_list'] = Merchant::getList([]);
        $data['search_data']   = $search_data;
        return view('admin.merchant.device_list', $data);
    }

    /**
     * 冻结、解冻设备
     * @param Request $request
     * @return array
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:08
     */
    public function device_status(Request $request)
    {
        $id            = $request->get('id');
        $admin_data    = $request->get('admin_data');
        $device_status = MerchantDevice::getValue(['id' => $id], 'status');
        if ($device_status === 1) {
            $tips   = "冻结了";
            $status = -1;
        } else {
            $tips   = "解冻了";
            $status = 1;
        }
        DB::beginTransaction();
        try {
            MerchantDevice::EditData(['id' => $id], ['status' => $status]);
            Logs::Operation(1, $admin_data['id'], "{$tips}id为【{$id}】的设备");
            DB::commit();
            return ['code' => 200, 'message' => '操作成功！'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code' => 500, 'message' => '操作失败，请稍后再试！'];
        }
    }


    /**
     * 编辑设备
     * @param Request $request
     * @return array
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:08
     */
    public function device_edit(Request $request)
    {
        $merchant_id = $request->get('merchant_id');
        $device_id   = $request->get('device_id');
        $device_uuid = $request->get('device_uuid');
        $address     = $request->get('address');
        $admin_data  = $request->get('admin_data');
        if ($merchant_id === 0) {
            return ['code' => 500, 'message' => '请选择归属商户'];
        }
        if (empty($device_uuid)) {
            return ['code' => 500, 'message' => '设备UUID不能为空！'];
        }
        $device_info = MerchantDevice::getOne(['id' => $device_id]);
        if ($device_info['merchant_id'] != $merchant_id || $device_info['device_uuid'] != $device_uuid) {
            if (MerchantDevice::checkRowExists(['merchant_id' => $merchant_id, 'device_uuid' => $device_uuid])) {
                return ['code' => 500, 'message' => '该合作商户的设备UUID重复，请重新设置UUID！'];
            }
        }
        DB::beginTransaction();
        try {
            MerchantDevice::EditData(['id' => $device_id], [
                'merchant_id' => $merchant_id,
                'device_uuid' => $device_uuid,
                'address'     => $address,
            ]);
            Logs::Operation(1, $admin_data['id'], "修改了设备ID为{$device_id}的信息");
            DB::commit();
            return ['code' => 200, 'message' => '编辑成功！'];
        } catch (\Exception $e) {
            \Log::debug($e);
            DB::rollBack();
            return ['code' => 500, 'message' => '编辑失败，请稍后再试！'];
        }
    }


    /**
     * 获取单个设备信息
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:08
     */
    public function device_info(Request $request)
    {
        $id          = $request->get('id');
        $device_info = MerchantDevice::where(['merchant_device.id' => $id])
            ->leftJoin('merchant', function ($join) {
                $join->on('merchant.id', '=', 'merchant_device.merchant_id');
            })->select(['merchant_device.*', 'merchant.company'])
            ->first();
        return ['code' => 200, 'message' => '获取成功', 'data' => $device_info];
    }

}
