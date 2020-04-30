<?php

namespace App\Console\Commands;

use App\Models\MerchantAdOrderTrackLog;
use App\Models\StatisticsScenePlay;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AdvertScene extends Command
{
    public $today;
    public $yesterday;
    /**
     * advert:scene
     * 按照场景进行统计和计算昨日的费用
     * @var string
     */
    protected $signature = 'advert:scene';

    /**
     * 控制台命令描述。
     *
     * @var string
     */
    protected $description = '按照场景计算昨日广告播放情况';

    /**
     * 创建一个新的命令实例。
     *
     * @return void
     */
    public function __construct()
    {
        // 获取今天的时间
        $this->today = Carbon::today()->timestamp;
        // 获取昨天的时间
        $this->yesterday = Carbon::yesterday()->timestamp;

        parent::__construct();
    }

    /**
     * 执行的方法
     */
    public function handle()
    {
        $yesterday = $this->yesterday;
        $today     = $this->today - 1;
        $data      = MerchantAdOrderTrackLog::whereBetween('created_at', [$yesterday, $today])->select(['merchant_id', 'ad_id', 'scene_id', DB::raw('count( id ) as play_num')])->groupBy(['merchant_id', 'ad_id', 'scene_id'])->get()->toArray();
        // 统计合作商户昨天的广告播放次数，按照不同的广告和不同的场景进行统计
        foreach ($data as $key => $val) {
            // 不存在昨日的数据报表，则进行统计记录入库
            try {
                if (!StatisticsScenePlay::checkRowExists([
                    'merchant_id' => $val['merchant_id'],
                    'scence_id'   => $val['scene_id'],
                    'ad_id'       => $val['ad_id'],
                    'created_at'  => $yesterday
                ])) {
                    StatisticsScenePlay::AddData([
                        'merchant_id' => $val['merchant_id'],
                        'scence_id'   => $val['scene_id'],
                        'ad_id'       => $val['ad_id'],
                        'play_num'    => $val['play_num'],
                        'created_at'  => $yesterday
                    ]);
                }
            } catch (\Exception $e) {
                \Log::debug("脚本(advert:scene)：统计合作商户昨天的广告播放次数，按照不同的广告和不同的场景进行统计");
                \Log::debug($e);
            }
        }
    }
}
