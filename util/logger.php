<?php

/**
 * Class Logger
 *
 * Provides static methods for logging access events, errors, and exceptions to separate log files.
 * This class is designed to centralize logging functionality for a PHP application, making it easier
 * to track access logs, error logs, and exception logs in a structured manner.
 *
 * Usage:
 * - Use Logger::logAccess($message) to log access events, including the client's IP address and a custom message.
 * - Use Logger::logError($errno, $errstr, $errfile, $errline) to log PHP errors with error number, message, file, and line.
 * - Use Logger::logException($exception) to log uncaught exceptions or throwables.
 *
 * Log files are defined by the LOGGER_PATH constant and are separated by type:
 * - access-logs.txt for access logs
 * - error-logs.txt for error logs
 * - exception-logs.txt for exception logs
 *
 * Methods:
 * @method static void logAccess(string $message)
 *   Logs an access event with the current date/time, client IP address, and a custom message to the access log file.
 *
 * @method static bool logError(int $errno, string $errstr, string $errfile, int $errline)
 *   Logs an error with error number, error message, file, and line number to the error log file.
 *   Returns true on success.
 *
 * @method static void logException(Throwable $exception)
 *   Logs an exception or throwable with the current date/time and exception details to the exception log file.
 */

class Logger
{
    private static $fileName = [
        'access' => LOGGER_PATH . 'access-logs.txt',
        'error' => LOGGER_PATH . 'error-logs.txt',
        'exception' => LOGGER_PATH . 'exception-logs.txt'
    ];
    private static $logger;

    private function __construct() {}

    public static function logAccess(String $message): void
    {
        $dateTime = new DateTime();
        $date = $dateTime->format("Y-m-d : h:i:s A");

        $ip = $_SERVER['REMOTE_ADDR'];

        $accessMessage = "[$date] - <$ip> $message" . PHP_EOL;

        $handle = fopen(self::$fileName['access'], 'a');
        if (!$handle) {
            throw new ErrorException('Cannot open ' . self::$fileName['access']);
        }

        fwrite($handle, $accessMessage);
        fclose($handle);
    }

    public static function logError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        $dateTime = new DateTime();
        $date = $dateTime->format("Y-m-d : h:i:s A");

        $errorMessage = "[$date] [$errno] -> $errstr @ $errfile : $errline" . PHP_EOL;
        error_log($errorMessage, 3, self::$fileName['error']);

        return true;
    }

    public static function logException(Throwable $exception): void
    {
        $dateTime = new DateTime();
        $date = $dateTime->format("Y-m-d : h:i:s A");

        $exceptionMessage = "[$date] -> $exception" . PHP_EOL;
        $handle = fopen(self::$fileName['exception'], 'a');
        if (!$handle) {
            throw new ErrorException('Cannot open ' . self::$fileName['exception']);
        }

        fwrite($handle, $exceptionMessage);
        fclose($handle);
    }
}
