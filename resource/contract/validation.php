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

enum VerificationStatus: String
{
    case P = 'pending';
    case V = 'verified';
    case R = 'rejected';
}

abstract class Validation
{
    abstract public static function getValidator();

    abstract public static function sanitizeData(array &$data): void;

    public function validate(string $fieldName, mixed $data, int $MIN = 8, int $MAX = 255, callable|array|null $callback = null): array
    {
        $fieldName = ucwords($fieldName);

        $validationResult = [
            'status' => true,
            'message' => "$fieldName is valid."
        ];

        // Empty string validation
        if (empty($data)) {
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

    public static function sanitize(array &$data, array $trimmableFields = []): void
    {
       foreach ($data as $key => $field) {
            if (preg_match('/^id$|_id$/', $key))
                $data[$key] = Id::toBinary($field);
            else if (preg_match('/email/', $key))
                $data[$key] = filter_var($field, FILTER_SANITIZE_EMAIL);
            else if (preg_match('/link/', $field))
                $data[$key] = filter_var($field, FILTER_SANITIZE_URL);
            else if (in_array($key, $trimmableFields, true))
                $data[$key] = trim($field);
        }
    }

    protected function validateId($param): array
    {
        if (!Id::validate($param)) {
            return [
                'status' => false,
                'message' => 'Id is invalid.'
            ];
        }
        return ['status' => true];
    }

    public function validateEmail($param): array
    {
        if (!filter_var($param, FILTER_VALIDATE_EMAIL)) {
            return [
                'status' => false,
                'message' => 'Invalid email format.'
            ];
        }
        return ['status' => true];
    }

    public function validateContact($param): array
    {
        if (preg_match('/^0-9\[\]\-\_\(\)\+\s\#]+@/', $param) === 1) {
            return [
                'status' => false,
                'message' => 'Contact number should only contain numbers, space, and special characters (\+, \[, \], \(, \), \-, \_, \#).'
            ];
        }
        return ['status' => true];
    }

    public function validateUrl(String $param): array
    {
        if (filter_var($param, FILTER_VALIDATE_URL)) {
            return [
                'status' => false,
                'message' => 'Invalid URL format.'
            ];
        }
        return ['status' => true];
    }

    public function validateVerification($param): array
    {
        if (!VerificationStatus::tryFrom($param)) {
            return [
                'status' => false,
                'message' => 'Invalid verification status.'
            ];
        }
        return ['status' => true];
    }
};
