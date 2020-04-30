<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class StatisticsScenePlay extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'statistics_scene_play';
    //主键
    protected $primaryKey = 'id';
}
