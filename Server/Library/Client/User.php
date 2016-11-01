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

    private $user_id, $username, $nickchen, $avatar;

    public function __construct($object = null)
    {
        if (!empty($object)) {
            $data = (array) $object;
            foreach($data as $key=>$val) {
                $this->$key = $val;
            }
        }
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }


}