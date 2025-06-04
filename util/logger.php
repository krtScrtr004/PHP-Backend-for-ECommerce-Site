<?php

class Logger
{
    private static $fileName = [
        'access' => LOGGER_PATH . 'access-logs.txt',
        'error' => LOGGER_PATH . 'error-logs.txt',
        'exception' => LOGGER_PATH . 'exception-logs.txt'
    ];
    private static $logger;

    private function __construct() {}

    public static function getLogger(): Logger
    {
        if (!isset(self::$logger))
            self::$logger = new Logger();

        return self::$logger;
    }

    public function logAccess(String $message): void
    {
        $dateTime = new DateTime();
        $date = $dateTime->format("Y-m-d : H:i:s A");

        $ip = $_SERVER['REMOTE_ADDR'];

        $accessMessage = "[$date] - <$ip> $message" . PHP_EOL;

        $handle = fopen(self::$fileName['access'], 'a');
        if (!$handle) {
            throw new ErrorException('Cannot open ' . self::$fileName['access']);
        }

        fwrite($handle, $accessMessage);
        fclose($handle);
    }

    public function logError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        $dateTime = new DateTime();
        $date = $dateTime->format("Y-m-d : H:i:s A");

        $errorMessage = "[$date] [$errno] -> $errstr @ $errfile : $errline" . PHP_EOL;
        error_log($errorMessage, 3, self::$fileName['error']);

        return true;
    }

    public function logException(Throwable $exception): void
    {
        $dateTime = new DateTime();
        $date = $dateTime->format("Y-m-d : H:i:s A");

        $exceptionMessage = "[$date] -> $exception" . PHP_EOL;
        $handle = fopen(self::$fileName['exception'], 'a');
        if (!$handle) {
            throw new ErrorException('Cannot open ' . self::$fileName['exception']);
        }

        fwrite($handle, $exceptionMessage);
        fclose($handle);
    }
}
