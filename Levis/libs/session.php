<?php

session_start();

class Session
{
    public static function getInstance()
    {
        static $instance;
        if (!$instance) {
            $instance = new static;
        }
        return $instance;
    }

    public static function get($name, $default = null)
    {
        return self::exists($name)? $_SESSION[$name] : $default;
    }

    public static function exists($name)
    {
        return isset($_SESSION[$name]);
    }

    public static function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }
}
