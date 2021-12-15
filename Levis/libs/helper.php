<?php
function resolveFilePath($directory_path, $file_path)
{
    return str_replace('\\', '/',  $directory_path).'/'. $file_path;
}

function camelize($str)
{
    return lcfirst(strtr(ucwords(strtr($str, ['_' => ' '])), [' ' => '']));
}

function underscore($str)
{
    return ltrim(strtolower(preg_replace('/[A-Z]/', '_\0', $str)), '_');
}
