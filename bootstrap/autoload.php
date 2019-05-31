<?php

define('APP_CORE_LOADED', true);
define('APP_APPLICATION_ROOT', dirname(__DIR__));
define('APP_CORE_ROOT', APP_APPLICATION_ROOT.'/app/core');
define('APP_RESOURCES_ROOT', APP_CORE_ROOT.'/resources');

require_once dirname(__DIR__).'/vendor/autoload.php';

define('APP_LOGS_DIR', APP_APPLICATION_ROOT . env('LOG_DIR', '/app/logs'));
