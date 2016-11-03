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

    public function getMulti($keys) {
        return $this->_connection->mget($keys);
    }

    public function del($key) {
        $this->_connection->del($key);
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this->_connection, $name)) {
            return call_user_func_array(array($this->_connection, $name), $arguments);
        }
    }

}