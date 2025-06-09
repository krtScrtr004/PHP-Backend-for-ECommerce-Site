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

function camelToKebabCase(String $str): String
{
    return preg_replace('/([a-z])([A-Z])/', '$1-$2', $str);
}

function camelToSnakeCase(String $str): String
{
    return preg_replace('/([a-z])([A-Z])/', '$1_$2',$str);
}
