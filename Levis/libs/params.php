<?php

class Params
{
    private $params = [];
    public static function getInstance()
    {
        static $instance;
        if (!$instance) {
            $instance = new static;
            $instance->init();
        }
        return $instance;
    }

    private function init()
    {
        $this->params = ($_SERVER['REQUEST_METHOD'] === 'POST')? $_POST : $_GET;
    }

    private function getParam($param_name)
    {
        if (isset($this->params[$param_name])) {
            return $this->params[$param_name];
        }

        return null;
    }

    public static function get($param_name, $default = null)
    {
        if (self::hasParam($param_name)) {
            return self::getInstance()->getParam($param_name);
        }

        return $default;
    }

    public static function getAll()
    {
        return self::getInstance()->params;
    }

    public static function hasParam($param_name)
    {
        $value = self::getInstance()->getParam($param_name);
        return $value !== null;
    }
}
