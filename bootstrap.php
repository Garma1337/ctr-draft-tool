<?php

use DraftTool\Lib\AutoLoader;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/lib/AutoLoader.php';

$autoloader = new AutoLoader(__DIR__ . '/');
$autoloader->register();