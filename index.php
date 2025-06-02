<?php

try {
    // Include all necessary files inside the resource directory
    foreach (glob(__DIR__ . '/resource/*.php') as $filename) {
        require_once $filename;
    }

    require_once __DIR__ . '/config/connection.php';
    $conn = DBConnection::getConnection();

    require_once __DIR__ . '/controller/user.php';
    $userAPI = UserAPI::getApi();

    $router = Router::getRouter();

    if (!file_exists('resource/routes.json')) {
        throw new ErrorException('No file with the name of routes.json found');
    }

    $routes = decodeData(file_get_contents('resource/routes.json'));
    foreach ($routes as $method => $paths) {
        foreach ($paths as $path => $action) {
            if (strcasecmp($action[0], 'user') === 0) {
                $action[0] = &$userAPI;
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

// $method = $_SERVER['REQUEST_METHOD'];
// switch ($method) {
//     case 'GET':
//         $userAPI->get();
//         break;
//     case 'POST':
//         $userAPI->post();
//         break;
//     case 'PUT':
//         $userAPI->put(['id' => 2]);
//         break;
//     case 'DELETE':
//         $userAPI->delete();
//         break;
//     default:
//         respond(status: 'fail', message: 'Bad request.', code: 404);
// }
