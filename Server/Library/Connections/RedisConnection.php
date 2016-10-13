<?php
class RedisConnection
{

    static $instance = [];

    private $redis = null;

    private function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect('0.0.0.0');
    }

    private static function getInstance($name = 'default') {
        if (!isset(self::$instance[$name])) {
            self::$instance[$name] = new self();
        }
        return self::$instance[$name];
    }

    private static function getConnection($name = 'default') {

    }

}