<?php
/**
 * Created by PhpStorm.
 * User: linyulin
 * Date: 16/10/27
 * Time: 下午1:56
 */

namespace Storages;


use Connections\RedisConnection;

class RecordStorage
{

    private $redis = null;
    private $id = 0;
    private $key = 'record_';
    private $max_count = 100;

    private static $instance = [];

    private function __construct($id)
    {
        $this->id = $id;
        $this->key = $this->key . $id;
        if ($this->redis == null)
            $this->redis = RedisConnection::getInstance("record");
    }

    public static function getInstance($id) {
        if (!isset(self::$instance[$id])) {
            self::$instance[$id] = new self($id);
        }
        return self::$instance[$id];
    }

    public function push($content) {
        if ($this->redis->lpush($this->key, $content)) {
            if ($this->redis->ltrim($this->key, 0, $this->max_count-1)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function range($start = 0, $stop = -1) {
        return $this->redis->lrange($this->key, $start, $stop);
    }


}