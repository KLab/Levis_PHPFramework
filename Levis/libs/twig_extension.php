<?php
declare(strict_types=1);

use Twig\TwigFunction;

class TwigExtension extends Twig\Extension\AbstractExtension
{
    // ルートindexまでのURLをここに入れる
    public const string APP_URL = "/index.php";

    public function getFunctions(): array
    {
        return [
            new TwigFunction('url', [$this, 'url']),
            new TwigFunction('getFilePath', [$this, 'getFilePath']),
            new TwigFunction('json_encode', [$this, 'json_encode'])
        ];
    }

    public function url(string $url): string
    {
        return self::APP_URL.$url;
    }

    public function getFilePath(string $path): string
    {
        return resolveFilePath(__DIR__, $path);
    }

    public function json_encode(mixed $json): string
    {
        return json_encode($json);
    }
}
