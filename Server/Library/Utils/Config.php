<?php
/**
 * 配置文件读取类
 *
 * @author: memory<service@im050.com>
 */

namespace Utils;

use \Exception;

class Config
{

    private static $config = [];
    private static $map = [];

    public static function get($key, $default = '') {

        if (isset(self::$map[$key])) {
            return self::$map[$key];
        } else {
            $value = self::createMap($key);
            if ($value == null) {
                if (!empty($default)) {
                    self::$map[$key] = $default;
                    return $default;
                }
                return FALSE;
            }
            return $value;
        }
    }

    public static function createMap($key) {
        if (strpos($key, ".") === FALSE) {
            throw new Exception("Config: the parameter lose '.'");
        }

        $key = explode(".", $key);
        $type = $key[0];
        //如果配置项不存在,则从文件中加载.
        if (!isset(self::$config[$type])) {
            self::loadConfig($type);
        }

        $value = self::$config;
        foreach($key as $val) {
            if (isset($value[$val])) {
                $value = $value[$val];
            } else {
                $value = null;
                break;
            }
        }

        if ($value != null)
            self::$map[implode(".",$key)] = $value;
        return $value;
    }

    public static function loadConfig($type) {
        $file = ROOT_PATH . 'Includes/Config/' . $type . ".php";
        if (!file_exists($file)) {
            throw new Exception("Config: load config file failed.\r\nFile: {$file}");
        }
        $config = include($file);
        self::$config[$type] = $config;
    }

}