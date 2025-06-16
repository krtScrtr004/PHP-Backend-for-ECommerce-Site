<?php

enum StoreType: String 
{
    case SP = 'sole_proprietorship';
    case CORP = 'corporation';
    case PT = 'partnership';
    case COOP = 'cooperative';
    case OP = 'one_person';
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
        self::sanitize($data, ['name', 'description']);
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
}