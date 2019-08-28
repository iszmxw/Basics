<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class AdInfo extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'adinfo';
    //主键
    protected $primaryKey = 'id';

    public static function getUrlAttribute($files)
    {
        if ($files) {
            $disk = Storage::disk('oss');
            $exists = $disk->has($files);
            if ($exists) {
                $url = env('OSS_CNAME') . trim($files);
            } else {
                $url = null;
            }
            return $url;
        }
    }
}
