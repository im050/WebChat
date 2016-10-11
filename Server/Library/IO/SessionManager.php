<?php
/**
 * Created by PhpStorm.
 * User: linyulin
 * Date: 16/10/10
 * Time: 上午11:22
 */

namespace IO;

use IO\Session;

class SessionManager
{

    private static $_instance = NULL;

    protected static $_session_list = array();

    protected static $_fd = array();

    private function __construct() {}

    public static function getInstance() {
        if (self::$_instance == NULL) {
            self::$_instance = self();
        }
        return self::$_instance;
    }

    public static function openSession() {
        do {
            $session_id = md5(uniqid(mt_rand(), TRUE));
            if (isset(self::$_session_list[$session_id])) {
                continue;
            }
            $session = new Session($session_id);
            self::$_session_list[$session_id] = & $session;
            return self::$_session_list[$session_id];
        }while(TRUE);
    }

    public static function bind($fd, Session $session) {
        self::$_fd[$fd] = $session;
    }

    public static function getSession($value) {
        if (is_numeric($value))
            return self::$_fd[$value];
        else
            return self::$_session_list[$value];
    }

    public static function getSessions() {
        return self::$_session_list;
    }

    public static function destroy(Session $session) {
        self::closeSession($session->getSessionId());
    }

    public static function closeSession($session_id) {
        unset(self::$_fd[self::getSession($session_id)->getFd()]);
        unset(self::$_session_list[$session_id]);
        //self::$_session_list[$session_id] = NULL;
        //echo $session_id;
    }

}