<?php

/**
 * Class DBConnection
 * 
 * DBConnection class provides a singleton-style interface for establishing and retrieving
 * a PDO connection to a MySQL database using predefined configuration settings.
 *
 * This class encapsulates the database connection details (hostname, username, password, dbname)
 * and ensures that only one PDO connection instance is created and reused throughout the application.
 *
 * Usage:
 *   $pdo = DBConnection::getConnection();
 *   // Use $pdo to interact with the database using PDO methods.
 *
 * Features:
 * - Uses PDO for secure and flexible database access.
 * - Sets PDO error mode to exception for better error handling.
 * - Sets default fetch mode to associative arrays.
 * - Disables emulated prepared statements for improved security.
 * - Automatically connects to the database on first use.
 *
 * Note:
 * - Designed for MySQL databases with UTF-8 (utf8mb4) character set.
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
