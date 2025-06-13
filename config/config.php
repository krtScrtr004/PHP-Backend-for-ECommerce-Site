<?php

declare(strict_types=1);

date_default_timezone_set('Asia/Hong_Kong');

/**
 * Define Path Constants
 */
define('BASE_PATH', dirname(__DIR__, 1));

define('API_PATH', BASE_PATH . '/API/');
define('CONFIG_PATH', BASE_PATH . '/config/');
define('DATA_PATH', BASE_PATH . '/data/');
define('LOGGER_PATH', BASE_PATH . '/log/');
define('RESOURCE_PATH', BASE_PATH . '/resource/');
define('ROUTER_PATH', BASE_PATH . '/router/');
define('UTIL_PATH', BASE_PATH . '/util/');

define('CONTRACT_PATH', RESOURCE_PATH . '/contract/');
define('IMPLEMENTATION_PATH', RESOURCE_PATH . '/implementation/');

/**
 * Include Files
 */
foreach (glob(CONFIG_PATH . '*.php') as $filename) {
    require_once $filename;
}
require_once UTIL_PATH . 'utility.php';

spl_autoload_register(function ($class) {
    $paths = [API_PATH, CONTRACT_PATH, CONFIG_PATH, IMPLEMENTATION_PATH, ROUTER_PATH, UTIL_PATH];
    foreach ($paths as $path) {
        // Turn camel case to kebab case
        $class = strtolower(camelToKebabCase($class));

        $file = $path . '/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file))
            require_once $file;
    }
});

/**
 * Error Handling Config
 */
ini_set('error_reporting', E_ALL);          // Report all errors
ini_set('display_errors', 0);               // Do not display errors on browser
set_error_handler(['Logger', 'logError']);
set_exception_handler(['Logger', 'logException']);

/**
 * Instantiate Classes
 */
$conn = DBConnection::getConnection();
$router = Router::getRouter();

$userAPI = UserApi::getApi();
$addressAPI = AddressAPI::getApi();
$productAPI = ProductAPI::getApi();
$productImageAPI = ProductImageAPI::getApi();
$orderAPI = OrderAPI::getApi();
$orderItemAPI = OrderItemApi::getApi();
