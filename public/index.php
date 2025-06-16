<?php

require_once dirname(__DIR__, 1) . '/config/config.php';

try {
    Logger::logAccess('Access ' . $_SERVER['PHP_SELF']);

    if (!file_exists(DATA_PATH . 'routes.json')) {
        throw new ErrorException('No file with the name of routes.json found');
    }

    $routes = decodeData(DATA_PATH . 'routes.json');
    foreach ($routes as $method => $paths) {
        foreach ($paths as $path => $action) {
            if (strcasecmp($action[0], 'user') === 0) {
                $action[0] = $userAPI;
            } else if (strcasecmp($action[0], 'userAddress') === 0) {
                $action[0] = $userAddressAPI;
            } else if (strcasecmp($action[0], 'store') === 0) {
                $action[0] = $storeAPI;
            } else if (strcasecmp($action[0], 'storeStaff') === 0) {
                $action[0] = $storeStaffAPI;
            } else if (strcasecmp($action[0], 'storeDocument') === 0) {
                $action[0] = $storeDocumentAPI;
            } else if (strcasecmp($action[0], 'storeAddress') === 0) {
                $action[0] = $storeAddressAPI;
            }else if (strcasecmp($action[0], 'product') === 0) {
                $action[0] = $productAPI;
            } else if (strcasecmp($action[0], 'productImage') === 0) {
                $action[0] = $productImageAPI;
            } else if (strcasecmp($action[0], 'order') === 0) {
                $action[0] = $orderAPI;
            } else {
                $action[0] = $orderItemAPI;
            }
            $router->register($path, $method, $action);
        }
    }

    $router->dispatch();
} catch (Exception $e) {
    Respond::respondException($e->getMessage());
}
