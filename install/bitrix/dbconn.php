<?php
if (!defined('APP_CORE_LOADED')) require_once(dirname(dirname(dirname(dirname(__DIR__)))).'/bootstrap/autoload.php');

/**
 * other code
 */


$DBHost = env('DB_HOST', 'localhost');
$DBLogin = env('DB_USER', '');
$DBPassword = env('DB_PASS', '');
$DBName = env('DB_NAME', '');
$DBDebug = env('DEBUG', false);


/**
 * other code
 */
