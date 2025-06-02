<?php

define('SESSION_LIFETIME', 3600);
define('SESSION_PATH', '\\');
define('SESSION_DOMAIN', 'localhost');
define('SESSION_SECURE', true);
define('SESSION_HTTPONLY', true);

ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);

session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path' => SESSION_PATH,
    'domain' => SESSION_DOMAIN,
    'secure' => SESSION_SECURE,
    'httponly' => SESSION_HTTPONLY
]);

if (session_status() !== PHP_SESSION_NONE)
    session_start();

if (!isset($_SESSION['lastRegen'])) {
    $_SESSION['lastRegen'] = time();
} else {
    $interval = 3600;
    // Regenerate session id after an hour (3600s)
    if (time() - $_SESSION['lastRegen'] > $interval) {
        $_SESSION['lastRegen'] = time();
        session_regenerate_id();
    }
}

