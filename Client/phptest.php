<?php
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);

$count = 1000;
while(($keys = $redis->scan($it, '', 1000))) {
    foreach($keys as $key) {
        if (!preg_match("/^record\_[0-9]+/", $key)) {
            $redis->del($key);
        }
    }
}
