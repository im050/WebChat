<?php
/**
 * Created by PhpStorm.
 * User: linyulin
 * Date: 16/10/13
 * Time: 下午1:02
 */

namespace Storages;


interface StorageInterface
{

    public static function getInstance();
    public function get($key);
    public function set($key, $value);
    public function getObject($key);
    public function setObject($key, $obj);

}