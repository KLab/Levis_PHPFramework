<?php
declare(strict_types=1);

function resolveFilePath(string $directory_path, string $file_path): string
{
    return str_replace('\\', '/',  $directory_path).'/'. $file_path;
}

function camelize(string $str): string
{
    return lcfirst(strtr(ucwords(strtr($str, ['_' => ' '])), [' ' => '']));
}

function underscore(string $str): string
{
    return ltrim(strtolower(preg_replace('/[A-Z]/', '_\0', $str)), '_');
}
