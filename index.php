<?php

declare(strict_types = 1);

use DraftTool\Lib\App;

/*
 * The CTR Draft Tool, made by Garma.
 * This tool uses a very simple, custom-made framework with only a handful external libraries:
 * - Smarty
 * - Doctrine DBAL
 * - jQuery
 * - Twitter Bootstrap (HTML, CSS)
 * - Feather (Icons)
 * - Symfony Console
 */

require_once __DIR__ . '/bootstrap.php';

App::dispatchRequest();
