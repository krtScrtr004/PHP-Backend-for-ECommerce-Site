<?php

class Respond
{
    private function __construct() {}

    private static function respond(String $status, String $message = '', array $data = [], int $code = 404): void
    {
        http_response_code($code);
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
        exit();
    }

    public static function respondSuccess(String $message = '', array $data = [], int $code = 200): void
    {
        self::respond('success', $message, $data, $code);
    }

    public static function respondFail(String $message = '', array $data = [], int $code = 400): void
    {
        self::respond('fail', $message, $data, $code);
    }

    public static function respondException(String $message = '', array $data = [], int $code = 500): void
    {
        self::respond('exception', $message, $data, $code);
    }
}
