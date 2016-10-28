<?php
/**
 * Created by PhpStorm.
 * User: linyulin
 * Date: 16/10/26
 * Time: 下午5:09
 */

namespace Client;


class User
{

    private $username, $nickchen, $avatar;

    public function __construct()
    {
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }


}