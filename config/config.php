<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__, 1));

define('CONFIG_PATH', BASE_PATH . '/config/');
define('CONTROLLER_PATH', BASE_PATH . '/controller/');
define('RESOURCE_PATH', BASE_PATH . '/resource/');
define('DATA_PATH', BASE_PATH . '/data/');
define('UTIL_PATH', BASE_PATH . '/util/');

require_once 'connection.php';
$conn = DBConnection::getConnection();

require_once CONTROLLER_PATH . 'router.php';
$router = Router::getRouter();

require_once RESOURCE_PATH . 'user.php';
$userAPI = UserAPI::getApi();

require_once UTIL_PATH . 'utility.php';
