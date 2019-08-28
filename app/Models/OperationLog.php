<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class OperationLog extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'operation_log';
    //主键
    protected $primaryKey = 'id';

    //分页获取数据
    public static function getPaginate($where = [], $field = ['operation_log.*', 'admin.account', 'admin.avatar', 'role.name as role_name'], $paginate = 10, $orderby = "id", $sort = "DESC")
    {
        if (empty($field)) {
            $field = '*';
        }
        $model = self::leftJoin('admin', function ($join) {
            $join->on('admin.id', '=', 'operation_log.account_id');
        })->leftJoin('role', function ($join) {
            $join->on('admin.role_id', '=', 'role.id');
        });
        $model = $model->select($field);
        if (!empty($where)) {
            $model = $model->where($where);
        }
        if (!empty($orderby)) {
            $model = $model->orderBy($orderby, $sort);
        }
        if (is_array($paginate)) {
            // 自定义分页
            $res = $model->paginate($paginate['limit'], $paginate['page']);
        } else {
            // 默认分页
            $res = $model->paginate($paginate);
        }
        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }
    }

}
