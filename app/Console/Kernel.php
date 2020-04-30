<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * 您的应用程序提供的Artisan命令。
     *
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\AdvertScene::class,
    ];

    /**
     * 定义应用程序的命令调度。
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 按照场景计算昨日广告播放情况
        $schedule->command('advert:scene')
            ->everyMinute();
    }

    /**
     * 注册应用程序的命令。
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
