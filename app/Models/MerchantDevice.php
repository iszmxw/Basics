<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class MerchantDevice extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'merchant_device';
    //主键
    protected $primaryKey = 'id';

    public function getCreatedAtAttribute($data)
    {
        return date('Y-m-d H:i:s', $data);
    }
}
