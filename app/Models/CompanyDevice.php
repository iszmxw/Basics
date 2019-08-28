<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyDevice extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'company_device';
    //主键
    protected $primaryKey = 'id';
}
