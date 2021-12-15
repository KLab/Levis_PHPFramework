<?php
require_once('libs/helper.php');
require_once("libs/bootstrap.php");
require_once("./test/test_base.php");

$test_files = glob('./test/*.php');
foreach ($test_files as $test_file) {
    $class_name = camelize(basename($test_file, '.php'));
    require_once($test_file);
    $class = new $class_name;
    foreach(get_class_methods($class_name) as $method) {
        $class->$method();
    }
}
