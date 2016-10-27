<?php
include 'Includes/init.php';

use Utils\Config;

echo Config::get('redis.default.host');
echo Config::get('redis.default.host');
echo Config::get('redis.default.host');

// 启动服务
$main_server = new \Server\MainServer('0.0.0.0', 8888);

$main_server->start();
