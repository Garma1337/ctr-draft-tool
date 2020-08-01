<?php

use DraftTool\Services\Translator;

return [
    'defaultLanguage' => Translator::LANGUAGE_ENGLISH,
    'db' => [
        'dbname'        => '',
        'user'          => '',
        'password'      => '',
        'host'          => '',
        'driver'        => 'pdo_mysql'
    ]
];
