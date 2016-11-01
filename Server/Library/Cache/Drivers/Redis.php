<?php
namespace Cache\Drivers;

use Connections\RedisConnection;
class Redis
{
    protected static $_instance = null;

    protected $_connection = null;

    private function __construct()
    {
        $this->_connection = RedisConnection::getInstance('cache');
    }

    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function get($key) {
        return $this->_connection->get($key);
    }

    public function set($key, $value) {
        $this->_connection->set($key, $value);
    }

    public function del($key) {
        $this->_connection->del($key);
    }

}