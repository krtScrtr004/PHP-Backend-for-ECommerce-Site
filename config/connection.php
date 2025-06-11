<?php

/**
 * Database Connection Class
 *
 * Usage:
 * $object = new ClassName($parameters);
 * $object->methodName($arguments);
 *
 */

class DBConnection
{
    /**
     * Summary of info
     * @var array
     */
    private static $info = [
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname' => 'rest',
    ];

    private static $connection = null;

    private function __construct()
    {
        $dsn = 'mysql: hostname=' . self::$info['hostname'] . '; dbname=' . self::$info['dbname'] . '; charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        self::$connection = new PDO($dsn, self::$info['username'], self::$info['password'], $options);
    }

    public static function getConnection(): PDO
    {
        if (self::$connection === null)
            new self();

        return self::$connection;
    }
}
