<?php
declare(strict_types=1);

session_start();

class Session
{
    public static function getInstance(): self
    {
        static $instance;
        if (!$instance) {
            $instance = new static;
        }
        return $instance;
    }

    public static function get(string $name, mixed $default = null): mixed
    {
        return self::exists($name)? $_SESSION[$name] : $default;
    }

    public static function exists(string $name): bool
    {
        return isset($_SESSION[$name]);
    }

    public static function set(string $name, mixed $value): void
    {
        $_SESSION[$name] = $value;
    }
}
