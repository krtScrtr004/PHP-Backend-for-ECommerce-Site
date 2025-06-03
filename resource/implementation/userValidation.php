<?php

require_once ABSTRACT_PATH . 'validation.php';

class userValidation implements Validation
{
    public static function validate(string $fieldName, mixed $data, int $MIN = 8, int $MAX = 255, callable|array|null $callback = null): array
    {
        $allowedFieldNames = ["id", "first name", "last name", "email", "password", "contact"];
        if (!in_array($fieldName, $allowedFieldNames))
            throw new Exception("$fieldName is not a valid field name.");

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

            /*
            * Allow only fields that are not in $exemptedFieldRegex 
            * Fields that are not allowed can have their own implementation of regex validation using the callback paramter
            */
            $exemptedFieldRegex = ["email", "password", "contact"];
            if (!in_array(strtolower($fieldName), $exemptedFieldRegex)) {
                // Allowed character validation
                if (preg_match('/[^a-zA-Z\s]+/', $data) === 1) {
                    $validationResult['status'] = false;
                    $validationResult['message'] = "$fieldName must only contain letters and spaces.";
                }
            }
        }
        // Callback function is defined
        else if ($callback) {
            $callbackReturn = null;
            if (is_callable($callback))
                $callbackReturn = call_user_func($callback, $data);
            else if (is_array($callback))
                $callbackReturn = call_user_func([new $callback[0], $callback[1]], $data);

            if (!$callbackReturn['status'])
                $validationResult = $callbackReturn;
        }

        return $validationResult;
    }

    public static function validateFields(array $data): array
    {
        // Get all fields to validate
        $presentFields = array_keys($data);

        $dataPath = DATA_PATH . 'validateUserFields.json';
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
                $params['callback'] = [self::class, $field['callback']];

            $validationResult = self::validate(...$params);
            if (!$validationResult['status'])
                return $validationResult;
        }
        return ['status' => true];
    }

    public static function sanitize(array &$data): void
    {
        if (!$data)
            throw new ErrorException('No data array to sanitize.');

        if (isset($data['id'])) $data['id'] = (int) $data['id'];
        if (isset($data['email'])) $data['email'] = filter_var($data['email'], FILTER_SANITIZE_EMAIL);

        $trimmableField = ['firstName', 'lastName', 'password', 'contact'];
        foreach ($trimmableField as $trimmable) {
            if (isset($data[$trimmable])) {
                $data[$trimmable] = trim($data[$trimmable]);
            }
        }
    }

    /* --Callback validator functions-- */

    private function validateId($param): array
    {
        if (!is_numeric($param)) {
            return [
                'status' => false,
                'message' => 'Id must be a number.'
            ];
        }
        return ['status' => true];
    }

    private function validatePassword($param): array
    {
        if (preg_match("/[^a-zA-Z0-9_!@'.-]/", $param) === 1) {
            return [
                'status' => false,
                'message' => "Password should only contain lower and uppercase characters, numbers, and special characters (_, -, !, @, ')."
            ];
        }
        return ['status' => true];
    }

    private function validateEmail($param): array
    {
        if (!filter_var($param, FILTER_VALIDATE_EMAIL)) {
            return [
                'status' => false,
                'message' => 'Invalid email format.'
            ];
        }
        return ['status' => true];
    }

    private function validateContact($param): array
    {
        if (preg_match('/^0-9\[\]\-\_\(\)\+\s\#]+@/', $param) === 1) {
            return [
                'status' => false,
                'message' => 'Contact number should only contain numbers, space, and special characters (\+, \[, \], \(, \), \-, \_, \#).'
            ];
        }
        return ['status' => true];
    }
}
