<?php

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

    public static function sanitize(array &$data): void
    {
        if (!isset($data))
            throw new ErrorException('No data array to sanitize.');

        if (isset($data['price']))
            $data['price'] = (int) ((float) $data['price'] * 100);

        $trimmableFields = ['name', 'description'];
        foreach ($trimmableFields as $trimmable) {
            if (isset($data[$trimmable]))
                $data[$trimmable] = trim($data[$trimmable]);
        }
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
