<?php

/**
 * Class UserValidation
 *
 * Singleton class for validating and sanitizing user-related data.
 * Inherits from Validation and provides methods to ensure user input is safe and conforms to expected formats.
 *
 * Usage:
 * - Use UserValidation::getValidator() to get the singleton instance.
 * - Use UserValidation::sanitizeData(&$data) to clean user data arrays before validation or storage.
 * - Use instance methods (e.g., validatePassword) to validate specific user fields.
 *
 * Methods:
 * - static getValidator(): Returns the singleton UserValidation instance.
 * - static sanitizeData(array &$data): Sanitizes and normalizes user data fields in the provided array.
 * - validatePassword($param): Validates that the password contains only allowed characters.
 */

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

    public static function sanitizeData(array &$data): void
    {
        parent::sanitize($data, ['firstName', 'lastName', 'password', 'contact']);
    }

    /* --Callback validator functions-- */

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
}
