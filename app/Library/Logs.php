<?php


namespace App\Library;


use App\Models\OperationLog;

class Logs
{
    /**
     * 操作日志添加
     * @param $type
     * @param $account_id
     * @param $content
     * @author：iszmxw <mail@54zm.com>
     * @Date 2019/10/15 0015
     * @Time：16:31
     */
    public static function Operation($type, $account_id, $content)
    {
        OperationLog::AddData(['type' => $type, 'account_id' => $account_id, 'content' => $content]);
    }
}