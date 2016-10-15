<?php
/**
 * redis连接类
 *
 * @author: memory<service@im050.com>
 */
namespace Connections;
class RedisConnection
{

    static $instance = [];

    public $redis = null;

    private function __construct()
    {
        $this->redis = new \Redis();
        if ($this->redis->connect('0.0.0.0'))
            return TRUE;
        else
            return FALSE;
    }

    public static function getInstance($name = 'default') {
        if (!isset(self::$instance[$name])) {
            self::$instance[$name] = new self();
        }
        return self::$instance[$name]->redis;
    }

}