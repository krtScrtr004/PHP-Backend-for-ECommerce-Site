<?php
function decodeData(String $rawData): array {
    if (!$rawData)
        throw new ErrorException('No raw JSON is defined.');

    $contents = json_decode($rawData, true);
    if (!$contents) 
        throw new JsonException('JSON contents cannot be decoded.');

    return $contents;
}

function respond(String $status, String $message = '', array $data = [], int $code = 404): void {
    http_response_code($code);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}