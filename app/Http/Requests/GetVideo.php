<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetVideo extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * 请求失败返回json数据
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        throw (new HttpResponseException(response()->json([
            'code' => 500,
            'message' => $validator->errors()->first()
        ], 200)));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lng' => 'required|numeric',
            'lat' => 'required|numeric',
            'ad_num' => 'required|numeric|between:1,12',
        ];
    }

    /**
     * 获取已定义的验证规则的错误消息。
     * @return array
     */
    public function messages()
    {
        return [
            'lng.required' => '缺少lng参数！',
            'lat.required' => '缺少lat参数！',
            'ad_num.required' => '请传入您要获取的广告数量！',
            'lng.numeric' => 'lng格式不正确！',
            'lat.numeric' => 'lat格式不正确！',
            'ad_num.numeric' => '请按照规范传入数量！',
            'ad_num.between' => 'ad_num必须在1到12之间',
        ];
    }
}
