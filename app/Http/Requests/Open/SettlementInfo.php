<?php

namespace App\Http\Requests\Open;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SettlementInfo extends FormRequest
{
    /**
     * 确定用户是否有权提出此请求。
     * @return bool
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:23
     */
    public function authorize()
    {
        return true;
    }

    /**
     * 请求失败返回json数据
     * @param Validator $validator
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:23
     */
    protected function failedValidation(Validator $validator)
    {
        throw (new HttpResponseException(response()->json([
            'code'    => 500,
            'message' => $validator->errors()->first()
        ], 200)));
    }

    /**
     * 获取应用于请求的验证规则。
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:23
     */
    public function rules()
    {
        return [
            'number'   => 'required|numeric|digits_between:18,24',
            'bankname' => 'required',
            'remarks'  => 'required',
            'code'     => 'required|numeric'
        ];
    }


    /**
     * 获取已定义的验证规则的错误消息。
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:23
     */
    public function messages()
    {
        return [
            'number.required'       => '银行卡号不能为空！',
            'bankname.required'     => '银行名称不能为空！',
            'remarks.required'      => '开户支行不能为空！',
            'number.numeric'        => '银行卡号格式不正确！',
            'number.digits_between' => '银行卡号格式不正确，请您仔细核对后在输入！',
            'code.required'         => '验证码不能为空！',
            'code.numeric'          => '验证码不正确！'
        ];
    }
}
