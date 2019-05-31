<?php

use \Dotenv\Dotenv;

if (file_exists(APP_APPLICATION_ROOT.'/.env')) {
    (new Dotenv(APP_APPLICATION_ROOT))->load();
}
