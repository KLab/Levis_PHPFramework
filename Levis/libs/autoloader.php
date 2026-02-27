<?php
declare(strict_types=1);

class AutoLoader
{
    private static bool $is_api = true;

    public static function setIsApi(bool $flag): void { self::$is_api = $flag; }

    private static function directories(): array
    {
        static $dirs;
        if (!$dirs) {
            $base = resolveFilePath(__DIR__, "../api/");
            $dirs = ["{$base}models"];
            if (self::$is_api) {
                $dirs[] = "{$base}controllers";
            } else {
                $cms_base = resolveFilePath(__DIR__, "../cms/");
                $dirs[] = "{$cms_base}models";
                $dirs[] = "{$cms_base}controllers";
            }
        }
        return $dirs;
    }

    public static function loadClass(string $class): void
    {
        foreach (self::directories() as $directory) {
            $file_name = underscore($class). ".php";
            $file_path = "{$directory}/{$file_name}";
            if (is_file($file_path)) {
                require_once $file_path;
                return;
            }
        }

        Logger::getInstance()->error("{$class} is not found");
    }
}

spl_autoload_register(['AutoLoader', 'loadClass']);
