<?php

/**
 * Session Configuration Script
 *
 * This script configures and manages PHP session settings for a secure web application.
 *
 * Features:
 * - Defines constants for session lifetime, path, domain, and security flags.
 * - Forces PHP to use only cookies for session management and enables strict mode.
 * - Sets session cookie parameters (lifetime, path, domain, secure, httponly) for enhanced security.
 * - Starts a session if one is not already active.
 * - Implements session ID regeneration every hour to mitigate session fixation attacks.
 *   - Stores the last regeneration timestamp in the session.
 *   - Regenerates the session ID if more than 3600 seconds (1 hour) have passed since the last regeneration.
 *
 * Usage:
 * - Include or require this script at the beginning of your PHP application before any output is sent.
 * - Ensures all session handling throughout the application follows these security and configuration standards.
 */

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

