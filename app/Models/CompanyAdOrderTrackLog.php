<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyAdOrderTrackLog extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'company_adorder_track_log';
    //主键
    protected $primaryKey = 'id';
}
