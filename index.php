<?php 

require_once __DIR__ . '/resource/utility.php';

require_once __DIR__ . '/config/connection.php';
$conn = DBConnection::getConnection();

require_once __DIR__ . '/controller/user.php';
$userAPI = UserAPI::getApi();

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        $userAPI->get();
        break;
    case 'POST':
        $userAPI->post();
        break;
    case 'PUT':
        break;
    case 'DELETE':
        break;
    default:
        respond(status: 'fail', message: 'Bad request.', code: 404);
}
