<?php

declare(strict_types=1);

/**
 * Define Path Constants
 */
define('BASE_PATH', dirname(__DIR__, 1));
define('CONFIG_PATH', BASE_PATH . '/config/');
define('API_PATH', BASE_PATH . '/api/');
define('ROUTER_PATH', BASE_PATH . '/router/');
define('RESOURCE_PATH', BASE_PATH . '/resource/');
define('CONTRACT_PATH', RESOURCE_PATH . '/contract/');
define('IMPLMENTAION_PATH', RESOURCE_PATH . '/implementation/');
define('DATA_PATH', BASE_PATH . '/data/');
define('UTIL_PATH', BASE_PATH . '/util/');
define('LOGGER_PATH', BASE_PATH . '/log/');

/**
 * Include Necessary Files
 */
foreach (glob(UTIL_PATH . '*.php') as $fileName) {
    require_once $fileName;
}

/**
 * Error Handling Config
 */
$logger = Logger::getLogger();
ini_set('error_reporting', E_ALL);          // Report all errors
ini_set('display_errors', 0);               // Do not display errors on browser
set_error_handler([$logger, 'logError']);

foreach (glob(CONFIG_PATH . '*.php') as $filename) {
    require_once $filename;
}
$conn = DBConnection::getConnection();

require_once ROUTER_PATH . 'router.php';
$router = Router::getRouter();

require_once API_PATH . 'user-api.php';
$userAPI = UserAPI::getApi();


