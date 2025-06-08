<?php

class AddrressValidation extends Validation
{
    public static function sanitize(array &$data): void {}

    private function validateHouseNo(int|String $param): array
    {
        if (preg_match('/[^\w#-]/', $param)) {
            return [
                'status' => false,
                'message' => 'House number can only contain letters, numbers, hash symbol(#), and hyphens(-).'
            ];
        }

        return ['status' => true];
    }

    private function validateStreet(String $param): array
    {
        if (preg_match('/[^\w\s\'\-]/', $param)) {
            return [
                'status' => false,
                'message' => 'Street can only contain letters, numbers, spaces, apostrophe(\'), and hyphens(-).'
            ];
        }

        return ['status' => true];
    }

    private function validateCity(String $param): array
    {
        if (preg_match('/[^a-zA-Z\s\'\-]/', $param)) {
            return [
                'status' => false,
                'message' => 'City can only contain letters, spaces, apostrophe(\'), and hyphens(-).'
            ];
        }
        return ['status' => true];
    }

    private function validateStateRegion(String $param): array
    {
        if (preg_match('/[^\w\s\-]/', $param)) {
            return [
                'status' => false,
                'message' => 'Street can only contain letters, numbers, spaces, and hyphens(-).'
            ];
        }
        return ['status' => true];
    }

    private function validateZip(String $param): array
    {
        if (preg_match('/[^(\d{4,})(\-\d+)?]/', $param)) {
            return [
                'status' => false,
                'message' => 'Invalid Postal / ZIP code.'
            ];
        }
        return ['status' => true];
    }
}
