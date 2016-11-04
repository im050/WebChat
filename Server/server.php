<?php
include 'Includes/init.php';

// 启动服务
$main_server = \Server\MainServer::getInstance();

$main_server->start();
