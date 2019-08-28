<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'company';
    //主键
    protected $primaryKey = 'id';
}
