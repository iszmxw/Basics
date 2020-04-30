<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'admin';
    //主键
    protected $primaryKey = 'id';

    /**
     * 获取用户的头像
     * @param $atavar
     * @return string
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:32
     */
    public function getAvatarAttribute($atavar)
    {
        if (empty($atavar)) {
            return config('app.url') . "/admin_style/images/avatars/avatar.png";
        } else {
            return config('app.url') . $atavar;
        }
    }


    /**
     * 获取分页数据
     * @param array $where
     * @param array $field
     * @param int $paginate
     * @param string $orderby
     * @param string $sort
     * @return bool
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:32
     */
    public static function getAccountPaginate($where = [], $field = [], $paginate = 1, $orderby = "id", $sort = "DESC")
    {
        if (empty($field)) {
            $field = '*';
        }
        $model = self::leftJoin('role', function ($join) {
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
