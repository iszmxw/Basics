<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'area';
    //主键
    protected $primaryKey = 'id';

}
