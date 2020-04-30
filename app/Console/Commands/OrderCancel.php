<?php

namespace App\Console\Commands;

use App\Models\MerchantAdOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class OrderCancel extends Command
{
    /**
     * 取消订单，用redis的key过期事件来处理过期订单
     * 将未上报的过期订单做过期处理
     *
     * @var string
     */
    protected $signature = 'order:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '过期广告订单处理';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //项目中有可能用的redis不是0，所以这里用env配置里面获取的
        $publish_num = env('REDIS_DATABASE', 1);
        $redis       = Redis::connection('ads');
        $redis->psubscribe(['__keyevent@' . $publish_num . '__:expired'], function ($message, $channel) {
            //$message 就是我们从获取到的过期key的名称
            $explode_arr = explode('_', $message);
            $prefix      = $explode_arr[0];
            if ($prefix == 'order') {
                try {
                    //这里就是编写过期的订单，过期后要如何处理的业务逻辑
                    //TODO
                    $order_id = $explode_arr[1];
                    $where    = ['id' => $order_id];
                    $res      = MerchantAdOrder::getOne($where);
                    if ($res) {
                        if ($res['status'] !== 1) {
                            MerchantAdOrder::EditData($where, ['status' => 2]);
                        }
                    }
                    echo $order_id . "\n";
                } catch (\Exception $e) {
                    Log::debug($e);
                }
            }
        });
    }
}
