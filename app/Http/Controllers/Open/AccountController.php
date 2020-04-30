<?php

namespace App\Http\Controllers\Open;

use App\Http\Requests\Open\SettlementInfo;
use App\Library\Logs;
use App\Models\Merchant;
use App\Models\MerchantSettlementInfo;
use App\Models\MerchantSettlementInfoLog;
use App\Models\Sms;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class AccountController extends Controller
{
    /**
     * 获取用户信息
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:09
     */
    public function info(Request $request)
    {
        $merchant           = $request->get('merchant');
        $merchant['avatar'] = "https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif";
        unset($merchant['password']);
        return ['code' => 200, 'message' => '获取用户信息成功！', 'data' => $merchant];
    }

    /**
     * 修改账户信息
     * @param Request $request
     * @return array
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:09
     */
    public function info_edit(Request $request)
    {
        $id           = $request->get('id');
        $old_password = $request->get('old_password');
        $new_password = $request->get('new_password');
        $re_password  = $request->get('re_password');
        $merchant     = $request->get('merchant');
        if (empty($old_password)) {
            return ['code' => 500, 'message' => '请输入旧密码'];
        }
        if (empty($new_password)) {
            return ['code' => 500, 'message' => '请输入新密码'];
        }
        if (empty($re_password)) {
            return ['code' => 500, 'message' => '请重复新密码'];
        }
        if ($new_password !== $re_password) {
            return ['code' => 500, 'message' => '两次密码不一致，请您确认后在输入！'];
        }
        if ($old_password !== decrypt($merchant['password'])) {
            return ['code' => 500, 'message' => '旧密码不正确！'];
        }
        \DB::beginTransaction();
        try {
            $res = Merchant::EditData(['id' => $id], ['password' => encrypt($new_password)]);
            // 从头部获取token
            $OpenToken = $request->header('Open-Token');
            // 接收第一次传过来的token
            $token = $request->get('token');
            // token最终结果
            $token = empty($token) ? $OpenToken : $token;
            // 更新用户的缓存信息
            Cache::put($token, $res, 120);
            \DB::commit();
            return ['code' => 200, 'message' => '操作成功'];
        } catch (\Exception $e) {
            \Log::debug($e);
            \DB::rollBack();
            return ['code' => 500, 'message' => '操作失败，请稍后再试'];
        }

    }

    /**
     * 获取基础配置
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:10
     */
    public function basic(Request $request)
    {
        $merchant       = $request->get('merchant');
        $data['appid']  = $merchant['appid'];
        $data['appkey'] = $merchant['appkey'];
        return ['code' => 200, 'message' => 'success', 'data' => $data];
    }


    /**
     * 刷新用户的appkey
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:10
     */
    public function Refresh(Request $request)
    {
        $merchant           = $request->get('merchant');
        $id                 = $merchant['id'];
        $appkey             = md5("ad$id" . time());
        $merchant['appkey'] = $appkey;
        try {
            // 从头部获取token
            $OpenToken = $request->header('Open-Token');
            // 接收第一次传过来的token
            $token = $request->get('token');
            // token最终结果
            $token = empty($token) ? $OpenToken : $token;
            $res   = Merchant::EditData(['id' => $merchant['id']], ['appkey' => $appkey]);
            if ($res) {
                // 更新用户的缓存信息
                Cache::put($token, $merchant, 120);
                return ['code' => 200, 'message' => '刷新成功！'];
            } else {
                return ['code' => 500, 'message' => '刷新失败，请稍后再试！'];
            }
        } catch (\Exception $e) {
            \Log::debug($e);
            return ['code' => 500, 'message' => '刷新失败，请稍后再试！'];
        }

    }

    /**
     * 合作商户的结算信息
     * @param Request $request
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:10
     */
    public function settlement_info(Request $request)
    {
        $merchant        = $request->get('merchant');
        $settlement_info = MerchantSettlementInfo::getOne(['merchant_id' => $merchant['id']], ['number', 'bankname', 'remarks']);
        return ['code' => 200, 'message' => 'ok', 'data' => $settlement_info];
    }

    /**
     * 商户修改银行卡信息
     * @param SettlementInfo $request
     * @return array
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:10
     */
    public function edit_settlement_info(SettlementInfo $request)
    {
        $merchant  = $request->get('merchant');
        $number    = $request->get('number');
        $bankname  = $request->get('bankname');
        $remarks   = $request->get('remarks');
        $code      = $request->get('code');
        $sms_where = ['mobile' => $merchant['mobile'], 'code' => $code, 'status' => 0];
        // 判断验证码是否存在系统中
        if (!Sms::checkRowExists($sms_where)) {
            return ['code' => 500, 'message' => '验证码不正确！'];
        }
        $old_info = MerchantSettlementInfo::getOne(['merchant_id' => $merchant['id']], ['number', 'bankname', 'remarks']);
        \DB::beginTransaction();
        try {
            if ($old_info['number'] != $number) {
                MerchantSettlementInfo::EditData(['merchant_id' => $merchant['id']], ['number' => $number]);
            }
            if ($old_info['bankname'] != $bankname) {
                MerchantSettlementInfo::EditData(['merchant_id' => $merchant['id']], ['bankname' => $bankname]);
            }
            if ($old_info['remarks'] != $remarks) {
                MerchantSettlementInfo::EditData(['merchant_id' => $merchant['id']], ['remarks' => $remarks]);
            }
            if ($old_info['number'] != $number || $old_info['bankname'] != $bankname || $old_info['remarks'] != $remarks) {
                MerchantSettlementInfoLog::AddData([
                    'merchant_id' => $merchant['id'],
                    'company'     => $merchant['company'],
                    'number'      => $old_info['number'],
                    'bankname'    => $old_info['bankname'],
                    'remarks'     => $old_info['remarks']
                ]);
            }
            Logs::Operation(2, $merchant['id'], "修改了提现信息");
            // 消费验证码
            Sms::EditData($sms_where, ['status' => 1]);
            \DB::commit();
            return ['code' => 200, 'message' => '操作成功'];
        } catch (\Exception $e) {
            \DB::rollBack();
            return ['code' => 500, 'message' => '修改失败，请稍后再试'];
        }
    }
}
