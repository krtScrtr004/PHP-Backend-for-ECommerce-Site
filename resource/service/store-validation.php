<?php

/**
 * Class StoreValidation
 *
 * Provides validation and sanitization utilities for store-related data.
 * Implements a singleton pattern to ensure a single instance is used throughout the application.
 * 
 * Methods:
 * - getValidator(): Returns the singleton instance of StoreValidation.
 * - sanitizeData(array &$data): Sanitizes the provided data array, focusing on keys: 'name', 'description', 'type', and 'vat_status'.
 * - validateType(string $param): Validates if the provided store type matches one of the defined StoreType enum values.
 * - validateVatStatus(string $param): Validates if the provided VAT status matches one of the defined StoreVatStatus enum values.
 * - validateTin(string $param): Validates if the provided TIN (Tax Identification Number) matches the required format (e.g., 123-456-789 or 123-456-789-000).
 *
 * Usage:
 *   $validator = StoreValidation::getValidator();
 *   $result = $validator->validateType('corporation');
 *   if (!$result['status']) { // handle invalid type }
 *
 * This class is intended to be used for validating and sanitizing store data before processing or persisting it.
 */

enum StoreType: String 
{
    case SP = 'sole_proprietorship';
    case CORP = 'corporation';
    case PT = 'partnership';
    case COOP = 'cooperative';
    case OP = 'one_person';
}

enum StoreVatStatus: String 
{
    case V = 'vat';
    case N = 'non';
}

class StoreValidation extends Validation
{
    private static $storeValidation;

    private function __construct() {}

    public static function getValidator(): StoreValidation
    {
        if (!isset(self::$storeValidation))
            self::$storeValidation = new self();

        return self::$storeValidation;
    }

    public static function sanitizeData(array &$data): void
    {
        self::sanitize($data, ['name', 'description', 'type', 'vat_status']);

    }

    public function validateType(String $param): array
    {
        if (!StoreType::tryFrom($param)) {
            return [
                'status' => false,
                'message' => 'Invalid store type.'
            ];
        }
        return ['status' => true];
    }

    // Document validator methods

    public function validateVatStatus(String $param): array 
    {
        if (!StoreVatStatus::tryFrom($param)) {
            return [
                'status' => false,
                'message' => 'Invalid store VAT type.'
            ];
        }
        return ['status' => true];
    }

    public function validateTin(String $param): array
    {
        if (preg_match('/(\d{3,3})-(\d{3,3})-(\d{3,3})(-(\d{3,3}))?/', $param) === 0) {
            return [
                'status' => false,
                'message' => 'Invalid TIN format.'
            ];
        }
        return ['status' => true];
    }
}