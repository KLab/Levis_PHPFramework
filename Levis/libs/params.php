<?php
declare(strict_types=1);

class Params
{
    private array $params = [];

    public static function getInstance(): self
    {
        static $instance;
        if (!$instance) {
            $instance = new static;
            $instance->init();
        }
        return $instance;
    }

    private function init(): void
    {
        $this->params = ($_SERVER['REQUEST_METHOD'] === 'POST')? $_POST : $_GET;
    }

    private function getParam(string $param_name): mixed
    {
        if (isset($this->params[$param_name])) {
            return $this->params[$param_name];
        }

        return null;
    }

    public static function get(string $param_name, mixed $default = null): mixed
    {
        if (self::hasParam($param_name)) {
            return self::getInstance()->getParam($param_name);
        }

        return $default;
    }

    public static function getAll(): array
    {
        return self::getInstance()->params;
    }

    public static function hasParam(string $param_name): bool
    {
        $value = self::getInstance()->getParam($param_name);
        return $value !== null;
    }
}
