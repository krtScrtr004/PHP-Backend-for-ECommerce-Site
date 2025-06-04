<?php
function decodeData(String $rawData): array
{
    if (!$rawData)
        throw new ErrorException('No raw JSON is defined.');

    $rawData = file_get_contents($rawData);
    $contents = json_decode($rawData, true);
    if (!$contents)
        throw new JsonException('JSON contents cannot be decoded.');

    return $contents;
}

function respond(String $status, String $message = '', array $data = [], int $code = 404): void
{
    http_response_code($code);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

function respondSuccess(String $message = '', array $data = [], int $code = 200): void
{
    respond('success', $message, $data, $code);
}

function respondFail(String $message = '', array $data = [], int $code = 400): void
{
    respond('fail', $message, $data, $code);
}

function respondException(String $message = '', array $data = [], int $code = 500): void
{
    respond('exception', $message, $data, $code);
}
