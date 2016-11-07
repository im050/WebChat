<?php
namespace Cache;

class Cache
{

    protected static $_driver = \Cache\Drivers\Redis::class;
    protected static $_instance = null;


    public static function init()
    {
        if (self::$_instance == null) {
            $driver = self::$_driver;
            self::$_instance = $driver::getInstance();
        }
        return self::$_instance;
    }

    public static function __callStatic($name, $arguments)
    {
        if (method_exists(self::$_instance, $name)) {
            return call_user_func_array(array(self::$_instance, $name), $arguments);
        } else {
            throw new \Exception("No '{$name}' method in Cache.");
        }
    }

}