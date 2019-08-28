<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class RoleRoute extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'role_route';
    //主键
    protected $primaryKey = 'id';

}
