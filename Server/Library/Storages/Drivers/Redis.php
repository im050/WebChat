<?php
/**
 * 存储驱动 - redis
 *
 * @author: memory<service@im050.com>
 */

namespace Storages\Drivers;

use Connections\RedisConnection;

class Redis
{

    protected static $_instance = [];

    protected $_connection = null;
    protected $_storage_key = '';

    private function __construct($name = 'storage')
    {
        $this->_storage_key = $name;
        $this->_connection = RedisConnection::getInstance('storage');
    }

    public static function getInstance($name)
    {
        if (!isset(self::$_instance[$name])) {
            self::$_instance[$name] = new self($name);
        }

        return self::$_instance[$name];
    }

    public function push($value)
    {
        return $this->_connection->sadd($this->_storage_key, $value);
    }

    public function del($value)
    {
        return $this->_connection->srem($this->_storage_key, $value);
    }

    public function all()
    {
        return $this->_connection->smembers($this->_storage_key);
    }

    public function smove($source, $destination, $member) {
        return $this->_connection->smove($source, $destination, $member);
    }

}