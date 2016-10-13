<?php
/**
 * Created by PhpStorm.
 * User: linyulin
 * Date: 16/10/13
 * Time: 下午1:01
 */

namespace Storages\Drivers;


use Storages\StorageInterface;

class RedisStorageHandler implements StorageInterface
{

    private $redis = null;

    private $instance = null;

    private function __construct()
    {
        $this->redis = new \Redis();
        $conn = $this->redis->connect('0.0.0.0', 6379, 10);
        if (!$conn) {
            throw new Exception('Redis connection failed');
        }
    }

    public static function getInstance() {

        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;

    }

    public function get($key)
    {
        // TODO: Implement get() method.
    }

    public function getObject($key)
    {
        // TODO: Implement getObject() method.
    }

    public function set($key, $value)
    {
        // TODO: Implement set() method.
    }

    public function setObject($key, $obj)
    {
        // TODO: Implement setObject() method.
    }

}