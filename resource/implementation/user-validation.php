<?php

class UserValidation extends Validation
{
    private static $userValidation;

    private function __construct() {}

    public static function getValidator(): UserValidation 
    {
        if (!isset(self::$userValidation))
            self::$userValidation = new self();

        return self::$userValidation;
    }

    public static function sanitize(array &$data): void
    {
        if (!isset($data))
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

    public function validateId($param): array
    {
        if (!is_numeric($param)) {
            return [
                'status' => false,
                'message' => 'Id must be a number.'
            ];
        }
        return ['status' => true];
    }

    public function validatePassword($param): array
    {
        if (preg_match("/[^a-zA-Z0-9_!@'.-]/", $param) === 1) {
            return [
                'status' => false,
                'message' => "Password should only contain lower and uppercase characters, numbers, and special characters (_, -, !, @, ')."
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
}
