<?php

namespace App\Middlewares;

use App\Middlewares\ErrorHandler as Error;

class HttpRequest {
    
    public string $uri;
    public array $param;

    public function __construct() {
        $this->uri = filter_var($_SERVER["REQUEST_URI"], FILTER_SANITIZE_URL);
        $this->param = explode("/", $this->uri);
        array_shift($this->param);
    }

    public function is(string $method) {
        return $method === $this->param[0];
    }

    public function registerProxyController(array $domains) {
        $domainNotFound = true;
        $requestedDomain = @parse_url(preg_replace("/\/proxy\//", "", $this->uri, 1))["host"];

        if ( empty($requestedDomain) ) {
            Error::json("Invalid domain!");
        }

        foreach ( $domains as $domain ) {
            if ( $requestedDomain === $domain ) {
                if ( !file_exists($file = __DIR__ . "/../controllers/$domain.php") ) {
                    Error::json("Controller not found!");
                }
                
                $proxyController = preg_replace_callback("/\w+/", function($matches) {
                    return ucfirst($matches[0]);
                }, preg_replace('/\./', '', $domain));
                
                $domainNotFound = false;
                break;
            }
        }

        if ( $domainNotFound ) {
            $file = __DIR__ . "/../controllers/common.php";
            $proxyController = "Common";
        }

        include $file;
        return new $proxyController($this->param);
    }
    
    public function redirect(string $to) {
        return header("Location: $to");
    }

}
