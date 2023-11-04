<?php

/**
 * PHP HTTP Proxy Server
 * 
 * @version 1.0.0
 * @author Haikal <haikalrafifas@github.com>
 */

use App\Middlewares\HttpRequest;

require __DIR__ . "/app/autoload.php";

$request = new HttpRequest();

/**
 * Domain names that are intended to have their
 * own proxy controller instead of the default ones.
 */
$domainRegistries = [
    "example.com"
];

$request->registerProxyController($domainRegistries);
