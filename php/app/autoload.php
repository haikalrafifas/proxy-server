<?php

spl_autoload_register(function ($class) {
    $prefix = 'App\\Middlewares\\';

    $len = strlen($prefix);
    if ( strncmp($prefix, $class, $len) !== 0 ) return;

    $relativeClass = substr($class, $len);

    $file = __DIR__ . '/middlewares/' . str_replace('\\', '/', $relativeClass) . '.php';

    !file_exists($file)? :require $file;
});
