<?php
namespace Cache;

class Cache
{

    protected static $_driver = \Cache\Drivers\Redis::class;

    public static function getInstance() {
        $driver = self::$_driver;
        $instance = $driver::getInstance();
        return $instance;
    }

}