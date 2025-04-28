<?php

$paths = [
    'updates',
    'storage/logos/clients',
    'storage/files',
    'application/storage/app/public',
    'application/storage/cache',
    'application/storage/cache/data',
    'application/storage/debugbar',
    'application/storage/framework/testing',
    'application/storage/logs',
    'application/storage/app/purifier',
    'application/storage/app/purifier/HTML',
];

foreach ($paths as $path) {
    $fullPath = __DIR__ . '/' . $path;
    echo $path . ' is ' . (is_writable($fullPath) ? 'Writable' : 'Not Writable') . '<br>';
}
