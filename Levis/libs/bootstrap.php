<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once(resolveFilePath(__DIR__, './config.php'));
require_once(resolveFilePath(__DIR__, './autoloader.php'));
require_once(resolveFilePath(__DIR__, './data_base.php'));
require_once(resolveFilePath(__DIR__, './logger.php'));
require_once(resolveFilePath(__DIR__, './params.php'));
require_once(resolveFilePath(__DIR__, './session.php'));
require_once(resolveFilePath(__DIR__, '../api/models/model_base.php'));
require_once(resolveFilePath(__DIR__, '../api/controllers/controller.php'));
