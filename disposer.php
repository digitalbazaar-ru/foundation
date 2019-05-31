#!/usr/bin/env php
<?php

define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);

define('DISPOSER_APP', true);

require_once __DIR__ . '/bootstrap/autoload.php';

$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/app/' . env('DISPOSER_SITE_FOLDER', 'public');

#require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

error_reporting(E_ERROR);

require_once __DIR__ . '/bootstrap/disposer.php';

@set_time_limit(0);

exit(\Foundation\Disposer::run());