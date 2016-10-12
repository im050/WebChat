<?php
/**
 * Created by PhpStorm.
 * User: linyulin
 * Date: 16/10/12
 * Time: 下午9:24
 */

namespace IO;


interface SessionInterface
{

    public function set_session_id();
    public function get_session_id();
    public function set(string $k, $v);
    public function add(string $k, $v);
    public function get(string $k, $v);
    public function setMulti(array $items);

}