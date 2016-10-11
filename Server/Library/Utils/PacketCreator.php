<?php
/**
 * Created by PhpStorm.
 * User: linyulin
 * Date: 16/10/11
 * Time: 上午1:50
 */

namespace Utils;


class PacketCreator
{
    private $attributes = [];

    public function __construct() {

    }

    public function toJSON() {
        return json_encode($this->attributes);
    }

    public function receiveMessage($content) {
        $this->make('receive_message', $content);
        return $this;
    }

    public function make($type, $content) {
        $this->attributes['type'] = 'receive_message';
        $this->attributes['content'] = $content;
        return $this;
    }

    public function __call($name, $arguments)
    {
        $pattern = "/^set([A-Z]+[a-zA-z0-9]*)$/";
        if (preg_match($pattern, $name, $matches)) {
            $attr = $matches[1];
            $attr = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1_',$attr));
            $this->__set($attr, $arguments[0]);
            return $this;
        }
    }

    public function __get($name) {
        if (isset($this->name)) {
            return $this->name;
        } else if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return null;
    }

    public function __set($name, $value) {
        if (isset($this->$name)) {
            $this->$name = $value;
        } else {
            $this->attributes[$name] = $value;
        }
    }
}