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
            if (preg_match('/(^id$|_id$|Id$)/', $key)) {
                $data[$key] = (int) $value;
            }
        }

        if (isset($data['status']))
            $data['status'] = trim(ucfirst($data['status']));
    }

    public function validateStatus(String $param): array
    {
        $isValidStatus = OrderStatus::tryFrom($param);
        if (!$isValidStatus) {
            return [
                'status' => false,
                'message' => 'Invalid order status [Options: pending, shipped, delivered, cancelled].'
            ];
        }

        return ['status' => true];
    }

    public function validateQuantity(float $param): array
    {
        if ($param < 1) {
            return [
                'status' => false,
                'message' => 'Quantity must be positive only.'
            ];
        } else if ($param > 99) {
            return [
                'status' => false,
                'message' => 'Maximum quantity is 99.'
            ];
        }

        return ['status' => true];
    }
}
