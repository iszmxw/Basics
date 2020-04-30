<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class MerchantAdvert extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'merchant_advert';
    //主键
    protected $primaryKey = 'id';

    protected $appends = ['user', 'complete_url'];

    /**
     * 返回格式化的时间
     * @return false|string
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:33
     */
    public function getCreatedAtAttribute()
    {
        $data = $this->attributes['created_at'];
        return date('Y-m-d H:i:s', $data);
    }


    /**
     * 返回完整的广告地址
     * @return string|null
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:33
     */
    public function getCompleteUrlAttribute()
    {
        $files = $this->getAttribute('url');
        if (!empty($files)) {
            $disk   = Storage::disk('oss');
            $exists = $disk->has($files);
            if ($exists) {
                $url = config('iszmxw.OSS_CNAME') . trim($files);
            } else {
                $url = null;
            }
        } else {
            $url = null;;
        }
        return $url;
    }


    /**
     * 返回首尾去除"，"的效果
     * @param $data
     * @return string
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:34
     */
    public function getDeviceIdAttribute($data)
    {
        return trim($data, ',');
    }

    /**
     * 获取用户名称
     * @return string
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:34
     */
    public function getUserAttribute()
    {
        $user_id = $this->getAttribute('user_id');
        if ($user_id === 1) {
            return '十万粉后台';
        } else {
            return '请处理一下用户名称';
        }
    }
}
