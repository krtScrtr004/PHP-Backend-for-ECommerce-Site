<?php

/**
 * Abstract class Validation
 *
 * Provides a base structure for validating and sanitizing data fields, typically used in resource or contract validation scenarios.
 * 
 * This class defines a set of methods to:
 * - Validate individual fields based on length, presence, and optional custom callbacks.
 * - Validate multiple fields against a schema defined in an external data file.
 * - Enforce implementation of a sanitizer for data cleaning in subclasses.
 * 
 * Usage:
 * - Extend this class to implement specific validation logic for different resources or data contracts.
 * - Implement the abstract methods `getValidator()` and `sanitize()` in the subclass.
 * 
 * Methods:
 * - abstract public static function getValidator(): Must be implemented to return a validator instance or configuration.
 * - public function validate(string $fieldName, mixed $data, int $MIN = 8, int $MAX = 255, callable|array|null $callback = null): array
 *      Validates a single field for presence, length, and optionally with a custom callback. Returns an array with validation status and message.
 * - public function validateFields(array $data, String $fileName): array
 *      Validates multiple fields based on a schema loaded from a file. Returns the first validation error or a success status.
 * - abstract public static function sanitize(array &$data): void
 *      Must be implemented to clean or sanitize the provided data array.
 */

abstract class Validation
{
    abstract public static function getValidator();

    public function validate(string $fieldName, mixed $data, int $MIN = 8, int $MAX = 255, callable|array|null $callback = null): array
    {
        $fieldName = ucwords($fieldName);

        $validationResult = [
            'status' => true,
            'message' => "$fieldName is valid."
        ];

        // Undefined / Null validation
        if (!$data) {
            $validationResult['status'] = false;
            $validationResult['message'] = "$fieldName is not defined";
        }
        // Empty string validation
        else if (empty($data)) {
            $validationResult['status'] = false;
            $validationResult['message'] = "$fieldName cannot be empty.";
        } else if (strcasecmp($fieldName, 'id') !== 0) {
            // Length requirement validation
            if (strlen($data) < $MIN || strlen($data) > $MAX) {
                $validationResult['status'] = false;
                $validationResult['message'] = "$fieldName must be between $MIN and $MAX only.";
            }
        }
        // Callback function is defined
        else if ($callback) {
            $callbackReturn = null;
            if (is_callable($callback))
                $callbackReturn = call_user_func($callback, $data);
            else if (is_array($callback))
                $callbackReturn = call_user_func([$callback[0], $callback[1]], $data);

            if (!$callbackReturn['status'])
                $validationResult = $callbackReturn;
        }

        return $validationResult;
    }

    public function validateFields(array $data, String $fileName): array
    {
        // Get all fields to validate
        $presentFields = array_keys($data);

        $dataPath = DATA_PATH . $fileName;
        if (!file_exists($dataPath)) {
            throw new ErrorException("$dataPath does not exists.");
        }

        $validateFields = decodeData($dataPath);
        foreach ($validateFields as $field) {
            // Skip field validation for fields that are not present
            if (!in_array($field['name'], $presentFields))
                continue;

            $params = [
                'fieldName' => $field['name'],
                'data' => $data[$field['data']],
            ];
            if ($field['min'])
                $params['MIN'] = $field['min'];
            if ($field['max'])
                $params['MAX'] = $field['max'];
            if ($field['callback'])
                $params['callback'] = [$this, $field['callback']];

            $validationResult = $this->validate(...$params);
            if (!$validationResult['status'])
                return $validationResult;
        }
        return ['status' => true];
    }

    abstract public static function sanitize(array &$data): void;

    protected function validateId($param): array
    {
        if (!is_numeric($param)) {
            return [
                'status' => false,
                'message' => 'Id must be a numeric.'
            ];
        }
        return ['status' => true];
    }
};
