<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyAdOrder extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'company_adorder';
    //主键
    protected $primaryKey = 'id';
}
