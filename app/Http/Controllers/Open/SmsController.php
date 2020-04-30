<?php

namespace App\Http\Controllers\Open;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use App\Http\Requests\Open\AliSms;
use App\Models\Merchant;
use App\Models\Sms;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SmsController extends Controller
{
    /**
     * 通过登录用户的手机获取验证码
     * @param Request $request
     * @return array|mixed
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:18
     */
    public function get_code(Request $request)
    {
        $sms_url = config('app.url') . "/api/open/sms/ali_sms";
        /**
         * 1、根据登录用户获取当前用户的手机号码、判断是否设置了手机号码，或者手机号码格式是否正确
         * 1、根据用户的手机号码，生成四位数或者六位数验证码
         * 2、将用户的手机号码和验证码存储到数据库做记录，status==0
         * 3、调用阿里大鱼，或者其他第三方平台发送验证码到用户手机上，发送成功status===1,发送失败status==-1
         * 4、用户收到验证码后，填写收到的验证码提交到我们服务器进行身份核对，查找用户在数据库中存储的验证码，是否未被是使用，或者未过期
         * 5、验证成功，即可进行下一步操作
         */
        $client = new Client();
        // 生成验证码
        $code = rand(1000, 9999);
        // 获取登录用户的信息
        $merchant = $request->get('merchant');
        // 查找用户的手机号码
        $mobile = Merchant::getValue(['id' => $merchant['id']], 'mobile');
        if (empty($mobile)) {
            return ['code' => 500, 'message' => '对不起检测到您的账户未设置手机号码，请您先设置手机号码'];
        }

        // 检查用户是否有未使用申请验证码
        if (Sms::checkRowExists(['mobile' => $mobile, 'status' => 0])) {
            $old_data = Sms::getOne(['mobile' => $mobile, 'status' => 0]);
            // 检测上一个验证码是否过期
            if (time() - $old_data['created_at'] > 180) {
                // 将上一验证码作过期处理
                Sms::EditData(['id' => $old_data['id']], ['status' => 2]);
            } elseif (time() - $old_data['created_at'] > 60) {
                return ['code' => 500, 'message' => '请不要重复频繁获取，请60秒后再试！'];
            }
        }

        //调用接口发送验证码操作，发送成功，进行下一步，失败，返回错误
        $http_re = $client
            ->post($sms_url, ['form_params' => ['mobile' => $mobile, 'code' => $code]])
            ->getBody()
            ->getContents();
        $re_data = json_decode($http_re, true);
        // 调用阿里大鱼发送错误直接返回错误信息
        if ($re_data['code'] == 500) {
            return $re_data;
        }
        DB::beginTransaction();
        try {
            // 生成验证码
            Sms::AddData(['mobile' => $mobile, 'code' => $code]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code' => 500, 'message' => '获取验证码失败，请稍后再试'];
        }
        return ['code' => 200, 'message' => '获取成功，请稍后在您的手机上查收！'];
    }


    /**
     * 通过传递过来的手机号码获取验证码
     * @param Request $request
     * @return array|mixed
     * @throws \Exception
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:18
     */
    public function get_mobile_code(Request $request)
    {
        $mobile  = $request->get('mobile');
        $sms_url = config('app.url') . "/api/open/sms/ali_sms";
        /**
         * 1、根据登录用户获取当前用户的手机号码、判断是否设置了手机号码，或者手机号码格式是否正确
         * 1、根据用户的手机号码，生成四位数或者六位数验证码
         * 2、将用户的手机号码和验证码存储到数据库做记录，status==0
         * 3、调用阿里大鱼，或者其他第三方平台发送验证码到用户手机上，发送成功status===1,发送失败status==-1
         * 4、用户收到验证码后，填写收到的验证码提交到我们服务器进行身份核对，查找用户在数据库中存储的验证码，是否未被是使用，或者未过期
         * 5、验证成功，即可进行下一步操作
         */
        $client = new Client();
        // 生成验证码
        $code = rand(1000, 9999);
        // 获取登录用户的信息
        $merchant = $request->get('merchant');
        if (empty($mobile)) {
            return ['code' => 500, 'message' => '请您输入手机号码！'];
        }
        // 查找用户的手机号码
        $old_mobile = Merchant::getValue(['id' => $merchant['id']], 'mobile');
        if ($mobile == $old_mobile) {
            return ['code' => 500, 'message' => '新手机号码，不能旧的手机号码相同！'];
        }


        //调用接口发送验证码操作，发送成功，进行下一步，失败，返回错误
        $http_re = $client
            ->post($sms_url, ['form_params' => ['mobile' => $mobile, 'code' => $code]])
            ->getBody()
            ->getContents();
        $re_data = json_decode($http_re, true);
        // 调用阿里大鱼发送错误直接返回错误信息
        if ($re_data['code'] == 500) {
            return $re_data;
        }

        // 检查用户是否有未使用申请验证码
        if (Sms::checkRowExists(['mobile' => $mobile, 'status' => 0])) {
            $old_data = Sms::getOne(['mobile' => $mobile, 'status' => 0]);
            // 检测上一个验证码是否过期
            if (time() - $old_data['created_at'] > 180) {
                // 将上一验证码作过期处理
                Sms::EditData(['id' => $old_data['id']], ['status' => 2]);
            } elseif (time() - $old_data['created_at'] < 60) {
                return ['code' => 500, 'message' => '请不要重频繁获取，请60秒后再试！'];
            }
        }
        DB::beginTransaction();
        try {
            // 生成验证码
            Sms::AddData(['mobile' => $mobile, 'code' => $code]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code' => 500, 'message' => '获取验证码失败，请稍后再试'];
        }
        return ['code' => 200, 'message' => '获取成功，请稍后在您的手机上查收！'];
    }


    /**
     * 调用阿里大鱼发送验证码
     * @param AliSms $request
     * @return array
     * @throws ClientException
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:18
     */
    public function ali_sms(AliSms $request)
    {
        $dayuAccessKey    = config('iszmxw.DaYuAccessKey');
        $dayuAccessSecret = config('iszmxw.DaYuAccessSecret');
        $mobile           = $request->get('mobile');
        $code             = $request->get('code');
        AlibabaCloud::accessKeyClient($dayuAccessKey, $dayuAccessSecret)
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->options([
                    'query' => [
                        'RegionId'      => "cn-hangzhou",
                        'PhoneNumbers'  => "$mobile",
                        'SignName'      => "粉丝万岁",
                        'TemplateCode'  => config('iszmxw.TemplateCode'),
                        'TemplateParam' => "{\"code\":\"$code\"}",
                    ],
                ])
                ->request();
            $re     = $result->toArray();
            if ($re['Message'] == "OK") {
                return ['code' => 200, 'message' => '验证码发送成功'];
            } else {
                \Log::debug($result);
                $message = self::ToMessage($re);
                return ['code' => 500, 'message' => $message];
            }
        } catch (ClientException $e) {
            return ['code' => 500, 'message' => "错误：" . $e->getErrorMessage()];
        } catch (ServerException $e) {
            return ['code' => 500, 'message' => "出错了：" . $e->getErrorMessage(), 'data' => [$mobile, $code, $request->all()]];
        }
    }


    /**
     * 错误消息类型封装
     * @param $re
     * @return string
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:18
     */
    public static function ToMessage($re)
    {
        switch ($re['Code']) {
            case 'isv.BUSINESS_LIMIT_CONTROL':
                if ($re['Message'] == "触发分钟级流控Permits:1") {
                    return "短时间内请不要，重复获取，请稍后再试";
                } elseif ($re['Message'] == "触发小时级流控Permits:5") {
                    return '对不起，您近期获取的验证码太多频繁了，请您一小时后再试！';
                } elseif ($re['Message'] == "触发天级流控Permits:10") {
                    return '对不起，系统检测到您一天内获取的验证码已经超过10条,请24小时后再试！';
                }
                break;
            default:
                return $re['Message'];
                break;
        }
    }
}
