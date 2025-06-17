<?php

/**
 * Class ProductValidation
 *
 * Provides validation and sanitization utilities for product-related data.
 * Implements the singleton pattern to ensure a single instance is used throughout the application.
 * Extends the abstract Validation class (not shown here).
 *
 * Responsibilities:
 * - Sanitizes product data arrays by trimming and converting specific fields.
 * - Converts 'price' to an integer value in cents.
 * - Validates product price to ensure it is within acceptable bounds.
 *
 * Methods:
 * - getValidator(): Returns the singleton instance of ProductValidation.
 * - sanitizeData(array &$data): Static method that sanitizes the provided product data array by:
 *      - Trimming whitespace from 'name' and 'description' fields.
 *      - Converting 'price' to an integer representing cents.
 * - validatePrice(float $param): Validates that the price is positive and does not exceed the maximum allowed value.
 *      Returns an array with validation status and message.
 */

class ProductValidation extends Validation
{
    private static $productValidation;

    private function __construct() {}

    public static function getValidator(): ProductValidation
    {
        if (!isset(self::$productValidation))
            self::$productValidation = new self();

        return self::$productValidation;
    }

    public static function sanitizeData(array &$data): void
    {
        self::sanitize($data, ['name', 'description']);

        if (isset($data['price']))
            $data['price'] = (int) ((float) $data['price'] * 100);
    }

    public function validatePrice(float $param): array
    {
        if ($param < 1.00) {
            return [
                'status' => false,
                'message' => 'Price must be positive only.'
            ];
        } else if ($param > 999999.999) {
            return [
                'status' => false,
                'message' => 'Maximum price is 999,999.999.'
            ];
        }

        return ['status' => true];
    }
}
