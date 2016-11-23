<?php
/**
 * 数据库连接类
 *
 * @author: memory<service@im050.com>
 */
namespace Connections;

use Server\MainServer;
use Utils\Config;
use \PDO;

class DatabaseConnection
{

    static $instance = [];

    public $db = null;

    private function __construct($dsn, $username, $password, $options = array())
    {
        $this->db = new PDO($dsn, $username, $password, $options);
        if ($this->db) {
            return true;
        } else {
            return false;
        }
    }

    public static function getInstance($name = 'default')
    {
        if (!isset(self::$instance[$name])) {
            $dsn = Config::get("database." . $name . ".dsn");
            $username = Config::get("database." . $name . ".user");
            $password = Config::get("database." . $name . ".pass");
            if (empty($dsn)) {
                return self::getInstance();
            }
            $options = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            );
            $server = MainServer::getInstance();
            print_ln("进程 [" . $server->getServer()->worker_id . "] 建立 Database[{$name}] 连接");
            self::$instance[$name] = new self($dsn, $username, $password, $options);
        }
        return self::$instance[$name]->db;
    }

}