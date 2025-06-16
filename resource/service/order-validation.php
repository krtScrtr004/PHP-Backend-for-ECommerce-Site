<?php

/**
 * Class OrderValidation
 *
 * A singleton class dedicated to validating and sanitizing order-related data.
 * Inherits from the base Validation class and provides specialized logic for order processing.
 *
 * Core Responsibilities:
 * - Sanitizes order data, including formatting the status field and casting IDs to integers.
 * - Validates order status against the allowed values defined in the OrderStatus enum.
 * - Ensures order quantity is within the acceptable range (1 to 99).
 *
 * Methods:
 * - getValidator(): Returns the singleton instance of OrderValidation, ensuring only one instance is used throughout the application.
 * - sanitizeData(array &$data): Static method that sanitizes the provided order data array in-place. It calls the base sanitize method and formats the 'status' field to have an uppercase first letter and trimmed whitespace.
 * - validateStatus(string $param): Checks if the provided status string matches one of the allowed order statuses using the OrderStatus enum. Returns an associative array with a boolean 'status' and an error 'message' if invalid.
 * - validateQuantity(float $param): Validates that the quantity is a positive number not exceeding 99. Returns an associative array with a boolean 'status' and an error 'message' if the value is out of bounds.
 *
 * Usage:
 * - Use getValidator() to obtain the singleton instance.
 * - Call sanitizeData() before processing order data to ensure consistency.
 * - Use validateStatus() and validateQuantity() to enforce business rules on order status and quantity fields.
 */

class OrderValidation extends Validation
{
    private static $orderValidation;

    private function __construct() {}

    public static function getValidator(): OrderValidation
    {
        if (!isset(self::$orderValidation))
            self::$orderValidation = new self();

        return self::$orderValidation;
    }

    public static function sanitizeData(array &$data): void
    {
        self::sanitize($data);

        if (isset($data['status']))
            $data['status'] = trim(ucfirst($data['status']));
    }

    public function validateStatus(String $param): array
    {
        $isValidStatus = OrderStatus::tryFrom($param);
        if (!$isValidStatus) {
            return [
                'status' => false,
                'message' => 'Invalid order status [Options: pending, shipped, delivered, cancelled].'
            ];
        }

        return ['status' => true];
    }

    public function validateQuantity(float $param): array
    {
        if ($param < 1) {
            return [
                'status' => false,
                'message' => 'Quantity must be positive only.'
            ];
        } else if ($param > 99) {
            return [
                'status' => false,
                'message' => 'Maximum quantity is 99.'
            ];
        }

        return ['status' => true];
    }
}
