<?php

namespace App\Middlewares;

class ErrorHandler {

    public static function json(string $message) {
        header("Content-Type: application/json");
        return exit(json_encode(["error" => $message]));
    }

}
