<?php
/**
 * redis连接类
 *
 * @author: memory<service@im050.com>
 */
namespace Connections;

use Utils\Config;

class RedisConnection
{

    static $instance = [];

    public $redis = null;

    private function __construct($name = 'default')
    {

        $host = Config::get('redis.' . $name . '.host', Config::get('redis.default.host'));
        $port = Config::get('redis.' . $name . '.port', Config::get('redis.default.port'));

        $this->redis = new \Redis();
        if ($this->redis->connect($host, $port))
            return TRUE;
        else
            return FALSE;
    }

    public static function getInstance($name = 'default')
    {
        if (!isset(self::$instance[$name])) {
            self::$instance[$name] = new self($name);
        }
        return self::$instance[$name]->redis;
    }

}