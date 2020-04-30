<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdvertAdd extends FormRequest
{
    /**
     * 验证前准备
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:21
     */
    public function prepareForValidation()
    {
        $type = $this->request->get('type');
        if ($type == "0") {
            throw (new HttpResponseException(response()->json([
                'code'    => 500,
                'message' => '请选择广告类型!'
            ])));
        }
    }

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
     * @Time：16:21
     */
    protected function failedValidation(Validator $validator)
    {
        throw (new HttpResponseException(response()->json([
            'code'    => 500,
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
            'user_id' => 'required|exists:user,id',
            'name'    => 'required',
            'desc'    => 'required',
            'type'    => 'required',
            'url'     => 'required',
            'price'   => 'required|numeric',
        ];
    }

    /**
     * 获取已定义的验证规则的错误消息。
     * @return array
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:21
     */
    public function messages()
    {
        return [
            'user_id.required' => '请选择广告归属用户！',
            'user_id.exists'   => '请选择广告归属用户！',
            'name.required'    => '请输入广告名称！',
            'desc.required'    => '请输入广告描述！',
            'type.required'    => '请选择广告类型！',
            'url.required'     => '请上传广告！',
            'price.required'   => '请设置广告单价！',
            'price.numeric'    => '请设置广告单价！',
        ];
    }
}
