<?php

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
}