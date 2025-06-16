<?php

/**
 * Class StaffValidation
 *
 * A singleton class extending the Validation base class, providing validation and sanitization
 * utilities specifically for staff-related data. This class ensures that only one instance
 * of StaffValidation exists throughout the application, accessible via the getValidator() method.
 *
 * Methods:
 * - getValidator(): Returns the singleton instance of StaffValidation, creating it if necessary.
 *   Use this method to access validation and sanitization functionality for staff data.
 *
 * - sanitizeData(array &$data): Static method that sanitizes the provided data array in place
 *   by delegating to the parent Validation::sanitize() method. Use this to clean input data
 *   before processing or storing staff information.
 */

class StaffValidation extends Validation
{
    private static $staffValidation;

    private function __construct() {}

    public static function getValidator(): StaffValidation
    {
        if (!isset(self::$staffValidation))
            self::$staffValidation = new self();

        return self::$staffValidation;
    }

    public static function sanitizeData(array &$data): void
    {
        parent::sanitize($data);
    }
}
