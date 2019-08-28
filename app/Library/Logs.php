<?php


namespace App\Library;


use App\Models\OperationLog;

class Logs
{
    // 操作日志添加
    public static function Operation($type, $account_id, $content)
    {
        OperationLog::AddData(['type' => $type, 'account_id' => $account_id, 'content' => $content]);
    }
}