<?php

use DraftTool\Lib\App;

require_once __DIR__ . '/bootstrap.php';

if (php_sapi_name() !== 'cli') {
    echo 'This file can only be executed from command line.';
    die();
}

$application = App::application();
$application->run();