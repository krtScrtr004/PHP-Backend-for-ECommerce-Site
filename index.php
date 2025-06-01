<?php 

require_once __DIR__ . '/resource/utility.php';

require_once __DIR__ . '/config/connection.php';
$conn = DBConnection::getConnection();

require_once __DIR__ . '/controller/user.php';
$userAPI = UserAPI::getApi();

$userAPI->get(['id' => '1']);
