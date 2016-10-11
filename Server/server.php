<?php

include 'Includes/init.php';

// 启动聊天服务器
$chat_server = new \Server\ChatServer();
$chat_server->start();
