<?php

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