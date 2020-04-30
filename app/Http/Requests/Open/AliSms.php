<?php

namespace App\Http\Requests\Open;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AliSms extends FormRequest
{
    /**
     * 确定用户是否有权提出此请求。
     * @return bool
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:22
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
     * @Time：16:22
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
     * @Time：16:22
     */
    public function rules()
    {
        return [
            'mobile' => 'required|numeric|digits_between:11,11',
            'code'   => 'required|numeric'
        ];
    }


    /**
     * 获取已定义的验证规则的错误消息。
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:22
     */
    public function messages()
    {
        return [
            'mobile.required'       => '手机号码不能为空！',
            'mobile.numeric'        => '手机号码格式不正确！',
            'mobile.digits_between' => '手机号码格式不正确，必须为11位！',
            'code.required'         => '验证码不能为空！',
            'code.numeric'          => '验证码必须为数组，建议为四到六为随机数！'
        ];
    }
}
