<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'role';
    //主键
    protected $primaryKey = 'id';

}
