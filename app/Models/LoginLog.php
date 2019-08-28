<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class LoginLog extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'login_log';
    //主键
    protected $primaryKey = 'id';

}
