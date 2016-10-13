<?php
/**
 * Created by PhpStorm.
 * User: linyulin
 * Date: 16/10/13
 * Time: 下午12:55
 */

namespace Storages;


class Storage implements StorageInterface
{

    private $driver_name = 'RedisStorageHandler';
    private $driver = null;
    private static $instance;

    private function __construct()
    {
        $this->driver = new ${$this->driver_name}();
    }

    public static function getInstance() {

        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;

    }

    public function get($key) {
        return $this->driver->get($key);
    }

    public function set($key, $value) {
        return $this->driver->set($key, $value);
    }

    public function getObject($key) {
        return $this->driver->getObject($key);
    }

    public function setObject($key, $obj) {
        return $this->driver->setObject($key, $obj);
    }
}