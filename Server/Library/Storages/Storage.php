<?php
/**
 * 存储类
 *
 * @author memory<service@im050.com>
 */

namespace Storages;

class Storage
{

    protected static $_driver = \Storages\Drivers\Redis::class;
    protected static $_storage_name = self::class;

    public static function getInstance($name) {
        $storage = self::$_driver;
        $instance = $storage::getInstance($name);
        return $instance;
    }

}