<?php
/**
 * You can create more array keys to add more connections.
 * like 'storage', 'record'
 * code example:
 * =================================================================
 * 'storage'=>[
 *     'host'=>'192.168.1.2',
 *     'port'=>5500
 * ];
 * $connection = \Connections\RedisConnection::getInstance('storage');
 * =================================================================
 */
return [
    'default'=>[
        'host'=>'localhost',
        'port'=>6379
    ]
];