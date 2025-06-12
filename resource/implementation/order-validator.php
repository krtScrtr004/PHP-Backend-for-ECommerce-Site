<?php

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

    public static function sanitize(array &$data): void
    {
        foreach ($data as $key => $value) {
            // Match only keys that end in '_id' or are 'id'
            if (preg_match('/(^id$|_id$)/', $key)) {
                $data[$key] = (int) $value;
            }
        }
    }
}
