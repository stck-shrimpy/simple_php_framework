<?php

namespace lib;

class Session
{
    private static $instance = null;

    private function __construct() {}

    public static function create()
    {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->start();
        }

        return self::$instance;
    }

    public function __set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function __get($key)
    {
        if (!isset($_SESSION[$key])) {
            return null;
        }

        return $_SESSION[$key];
    }

    public function __isset($key)
    {
        return isset($_SESSION[$key]);
    }

    public function getId()
    {
        return session_id();
    }

    public function getName()
    {
        return session_name();
    }

    public function unset($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public function destroy()
    {
        session_destroy();
    }

    private function start()
    {
        session_start();
    }
}
