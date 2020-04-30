<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantSettlementInfoLog extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'merchant_settlement_info_log';
    //主键
    protected $primaryKey = 'id';

    public function getCreatedAtAttribute($data)
    {
        return date('Y-m-d H:i:s', $data);
    }
}
