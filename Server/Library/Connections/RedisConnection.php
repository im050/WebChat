<?php
/**
 * redis连接类
 *
 * @author: memory<service@im050.com>
 */
namespace Connections;

use Server\MainServer;
use Utils\Config;

class RedisConnection
{

    static $instance = [];

    public $redis = null;

    private function __construct($host, $port)
    {
        $this->redis = new \Redis();
        if ($this->redis->connect($host, $port))
            return TRUE;
        else
            return FALSE;
    }

    public static function getInstance($name = 'default')
    {
        if (!isset(self::$instance[$name])) {
            $host = Config::get('redis.' . $name . '.host', '');
            $port = Config::get('redis.' . $name . '.port', '');

            if (empty($host)) {
                return self::getInstance();
            }

            $server = MainServer::getInstance();
            print_ln("进程 [" . $server->getServer()->worker_id . "] 建立 Redis[{$name}] 连接");
            self::$instance[$name] = new self($host, $port);
        }
        return self::$instance[$name]->redis;
    }

}