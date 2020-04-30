<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Merchant extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'merchant';
    //主键
    protected $primaryKey = 'id';
}
