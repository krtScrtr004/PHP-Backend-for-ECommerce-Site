<?php

/**
 * Class Respond
 *
 * Utility class for sending standardized JSON HTTP responses in a RESTful API.
 * This class provides static methods to send success, failure, or exception responses,
 * each with customizable status, message, data, and HTTP status code.
 *
 * Usage:
 * - Use Respond::respondSuccess() to send a successful response (HTTP 200 by default).
 * - Use Respond::respondFail() to send a failure response (HTTP 400 by default).
 * - Use Respond::respondException() to send an exception/error response (HTTP 500 by default).
 *
 * Each response method outputs a JSON object with the following structure:
 * {
 *   "status": "success|fail|exception",
 *   "message": "Custom message",
 *   "data": [Optional data array]
 * }
 *
 * All methods terminate script execution after sending the response.
 *
 * Methods:
 * @method static void respondSuccess(string $message = '', array $data = [], int $code = 200)
 *         Sends a success response with optional message, data, and HTTP status code.
 *
 * @method static void respondFail(string $message = '', array $data = [], int $code = 400)
 *         Sends a failure response with optional message, data, and HTTP status code.
 *
 * @method static void respondException(string $message = '', array $data = [], int $code = 500)
 *         Sends an exception/error response with optional message, data, and HTTP status code.
 */

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
