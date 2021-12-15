<?php

use Twig\TwigFunction;

class TwigExteinsion extends Twig\Extension\AbstractExtension
{
    // ルートindexまでのURLをここに入れる
    const APP_URL = "/index.php";
    public function getFunctions()
    {
        return [
            new TwigFunction('url', [$this, 'url']),
            new TwigFunction('getFilePath', [$this, 'getFilePath']),
            new TwigFunction('json_encode', [$this, 'json_encode'])
        ];
    }

    public function url($url)
    {
        return self::APP_URL.$url;
    }

    public function getFilePath($path)
    {
        return resolveFilePath(__DIR__, $path);
    }

    public function json_encode($json)
    {
        return json_encode($json);
    }
}
