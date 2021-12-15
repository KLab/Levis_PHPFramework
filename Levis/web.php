<?php
require_once('libs/bootstrap.php');
$url_info = $_SERVER['PATH_INFO'];

$parsed_url = explode('/', trim($url_info, '/'));

$method = count($parsed_url) >= 2 ? $parsed_url[1] : 'index';
$controller = camelize($parsed_url[0]. '_controller');
$view = resolveFIlePath(__DIR__, "view/$parsed_url[0]/$method.php");

$logger = Logger::getInstance();
try {
    extract(Params::getInstance()->getAll(), EXTR_OVERWRITE);
    if ($parsed_url[0] === 'index') {
        include_once('./index.html');
        return;
    }
    $controller = new $controller;
    $controller->$method();
    extract($controller->getVars(), EXTR_OVERWRITE);
    include_once $view;
} catch (Exception $e) {
    $logger->error($e->getMessage());
}