<?php
/**
 * 数据格式处理类
 *
 * @author: memory<service@im050.com>
 */

namespace Utils;


class PacketCreator
{
    private $attributes = [];

    public function __construct()
    {

    }

    /**
     * 将数据转换为JSON
     * @return string
     */
    public function toJSON()
    {
        return json_encode($this->attributes, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 群聊信息
     * @param $content
     * @return $this
     */
    public function receiveMessage($content)
    {
        $this->make('receive_message', $content);
        return $this->toJSON();
    }

    /**
     * 快速处理信息
     * @param $type
     * @param $content
     * @return $this
     */
    public function make($type, $content)
    {
        $this->attributes['type'] = $type;
        $this->attributes['content'] = $content;
        return $this->toJSON();
    }

    public function error($message, $error_code = 0) {
        $this->attributes['type'] = 'error';
        $this->attributes['content'] = array(
            'error_code' => $error_code,
            'message' => $message
        );
        return $this->toJSON();
    }

    /**
     * 链式设置属性
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        $pattern = "/^set([A-Z]+[a-zA-z0-9]*)$/";
        if (preg_match($pattern, $name, $matches)) {
            $attr = $matches[1];
            $attr = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1_', $attr));
            $this->__set($attr, $arguments[0]);
            return $this;
        }
    }

    public function __get($name)
    {
        if (isset($this->name)) {
            return $this->name;
        } else if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return null;
    }

    public function clear()
    {
        $this->attributes = [];
    }

    public function __set($name, $value)
    {
        if (isset($this->$name)) {
            $this->$name = $value;
        } else {
            $this->attributes[$name] = $value;
        }
    }
}