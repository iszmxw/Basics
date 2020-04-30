<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantAdOrder extends Base
{
    use SoftDeletes;
    //表名
    protected $table = 'merchant_adorder';
    //主键
    protected $primaryKey = 'id';

    public function getCreatedAtAttribute($data)
    {
        return date('Y-m-d H:i:s', $data);
    }

    /**
     * 分页获取数据
     * @param array $where
     * @param array $field
     * @param int $paginate
     * @param string $orderby
     * @param string $sort
     * @return bool
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:33
     */
    public static function getMonitorAdvertOrderPaginate($where = [], $field = [], $paginate = 10, $orderby = "id", $sort = "DESC")
    {
        if (empty($field)) {
            $field = '*';
        }
        $model = self::select($field);
        if (!empty($where)) {
            $model = $model->where($where);
        }
        $model = $model->leftJoin('merchant', function ($join) {
            $join->on('merchant.id', '=', 'merchant_adorder.merchant_id');
        });
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
