<?php
include 'Includes/init.php';

// 启动服务
$main_server = new \Server\MainServer('0.0.0.0', 8888);

$main_server->start();
