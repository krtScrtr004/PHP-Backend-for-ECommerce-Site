<?php

require_once dirname(__DIR__, 1) . '/config/config.php';

try {
    $logger->logAccess('Access ' . $_SERVER['PHP_SELF']);

    if (!file_exists(DATA_PATH . 'routes.json')) {
        throw new ErrorException('No file with the name of routes.json found');
    }

    $routes = decodeData(DATA_PATH . 'routes.json');
    foreach ($routes as $method => $paths) {
        foreach ($paths as $path => $action) {
            if (strcasecmp($action[0], 'user') === 0) {
                $action[0] = $userAPI;
            } else {
                // TODO: Add the product api obect here
            }
            $router->register($path, $method, $action);
        }
    }

    $router->dispatch();
} catch (Exception $e) {
    respondException($e->getMessage());
}

