<?php

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
