<?php

return [
    'ip' => '0.0.0.0',
    'port' => 8888,
    //swoole配置
    //更多配置可参考官方文档
    'swoole_config' => [
        //心跳检测间隔
        'heartbeat_check_interval' => 60,
        //心跳检测超时时间
        'heartbeat_idle_time' => 600,
        //进程数
        'worker_num' => 4,
        //Task进程数
        'task_worker_num' => 2
    ]
];