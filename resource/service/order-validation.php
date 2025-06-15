<?php

/**
 * Class OrderValidation
 *
 * Singleton class responsible for validating and sanitizing order-related data.
 * Extends the base Validation class to provide specific validation logic for orders.
 *
 * Responsibilities:
 * - Ensures order data fields are properly sanitized (e.g., casting IDs to integers, formatting status).
 * - Validates order status against allowed values defined in the OrderStatus enum.
 * - Validates order quantity to ensure it is within acceptable bounds.
 *
 * Methods:
 * - getValidator(): Returns the singleton instance of OrderValidation.
 * - sanitize(array &$data): Static method to sanitize order data in-place, converting IDs to integers and formatting the status field.
 * - validateStatus(string $param): Validates the provided status string against allowed order statuses. Returns an array indicating validation result and message.
 * - validateQuantity(float $param): Validates that the quantity is positive and does not exceed the maximum allowed (99). Returns an array indicating validation result and message.
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
