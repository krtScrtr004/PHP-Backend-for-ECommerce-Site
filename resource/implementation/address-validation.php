<?php

class AddressValidation extends Validation
{
    private static $addressValidation;

    private function __construct() {}
    public static function getValidator(): AddressValidation
    {
        if (!isset(self::$addressValidation))
            self::$addressValidation = new self();

        return self::$addressValidation;
    }
    public static function sanitize(array &$data): void
    {
        if (!isset($data))
            throw new ErrorException('No data array to sanitize.');

        $trimmableFields = ['houseNo', 'street', 'city', 'region', 'postalCode', 'country'];
        foreach ($trimmableFields as $trimmable) {
            if (isset($data[$trimmable]))
                $data[$trimmable] = trim($data[$trimmable]);
        }
    }

    public function validateHouseNo(int|String $param): array
    {
        if (preg_match('/[^\w#-]/', $param)) {
            return [
                'status' => false,
                'message' => 'House number can only contain letters, numbers, hash symbol(#), and hyphens(-).'
            ];
        }

        return ['status' => true];
    }

    public function validateStreet(String $param): array
    {
        if (preg_match('/[^\w\s\'\-]/', $param)) {
            return [
                'status' => false,
                'message' => 'Street can only contain letters, numbers, spaces, apostrophe(\'), and hyphens(-).'
            ];
        }

        return ['status' => true];
    }

    public function validateCity(String $param): array
    {
        if (preg_match('/[^a-zA-Z\s\'\-]/', $param)) {
            return [
                'status' => false,
                'message' => 'City can only contain letters, spaces, apostrophe(\'), and hyphens(-).'
            ];
        }
        return ['status' => true];
    }

    public function validateRegion(String $param): array
    {
        if (preg_match('/[^\w\s\-]/', $param)) {
            return [
                'status' => false,
                'message' => 'Region can only contain letters, numbers, spaces, and hyphens(-).'
            ];
        }
        return ['status' => true];
    }

    public function validatePostalCode(String $param): array
    {
        if (preg_match('/[^(\d{4,})(\-\d+)?]/', $param)) {
            return [
                'status' => false,
                'message' => 'Invalid Postal code format.'
            ];
        }
        return ['status' => true];
    }
}
