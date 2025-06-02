<?php

require_once __DIR__ . '/config/config.php';

try {
    if (!file_exists(DATA_PATH . 'routes.json')) {
        throw new ErrorException('No file with the name of routes.json found');
    }

    $routes = decodeData(file_get_contents(DATA_PATH . 'routes.json'));
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
    respond(status: 'exception', message: $e->getMessage(), code: 500);
}

