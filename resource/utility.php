<?php
function respond(String $status, String $message = '', array $data = [], int $code = 404): void {
    http_response_code($code);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}