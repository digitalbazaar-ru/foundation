<?php

use Foundation\Disposer;

#Disposer::add(new Command());

if (file_exists(APP_APPLICATION_ROOT . '/app/commands/local.php')) {
    require_once APP_APPLICATION_ROOT . '/app/commands/local.php';
}
