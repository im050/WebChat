<?php
/**
 * Created by PhpStorm.
 * User: linyulin
 * Date: 16/10/10
 * Time: 上午11:11
 */

namespace Utils;


class AutoLoader
{
    public static $loader;

    private function __construct(){
        spl_autoload_register(array($this, 'load'));
    }

    public static function init() {
        if (self::$loader == null) {
            self::$loader = new self();
        }
        return self::$loader;
    }

    public function load($class) {

        $file = dirname(__DIR__);

        $class = str_ireplace('\\', '/', $class);

        $class_path = $file . '/' . $class . '.php';

        if (file_exists( $class_path )) {
            require $class_path;
        } else {
            throw new \Exception('Not Found Class { '.$class_path.' }');
        }

    }
}