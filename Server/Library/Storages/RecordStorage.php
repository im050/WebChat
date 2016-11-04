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

    /**
     * 获取指定房间聊天记录实例
     *
     * @param $room_id 房间ID
     * @return mixed
     */
    public static function getInstance($room_id)
    {
        if (!isset(self::$instance[$room_id])) {
            self::$instance[$room_id] = new self($room_id);
        }
        return self::$instance[$room_id];
    }

    /**
     * 插入记录
     * @param $content
     * @return bool
     */
    public function push($content)
    {
        if ($this->redis->lpush($this->key, $content)) {
            if ($this->redis->ltrim($this->key, 0, $this->max_count - 1)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 获取记录
     * @param int $start
     * @param int $stop
     * @return mixed
     */
    public function range($start = 0, $stop = -1)
    {
        return $this->redis->lrange($this->key, $start, $stop);
    }

    public function getAll()
    {
        return $this->range();
    }


}