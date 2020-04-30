<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class DeviceScene extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'device_scene';
    //主键
    protected $primaryKey = 'id';
}
