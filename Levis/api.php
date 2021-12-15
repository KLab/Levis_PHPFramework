<?php
require_once('libs/bootstrap.php');
$url_info = $_SERVER['PATH_INFO'];
$parsed_url = explode('/', trim($url_info, '/'));
$controller = camelize($parsed_url[0]. '_controller');
$method = count($parsed_url) >= 2 ? $parsed_url[1] : 'index';
$logger = Logger::getInstance();

try {
    $controller = new $controller;
    $method = $method;
    $controller->$method();
    echo json_encode($controller->getVars());
} catch (Exception $e) {
    $logger->error($e->getMessage());
}
