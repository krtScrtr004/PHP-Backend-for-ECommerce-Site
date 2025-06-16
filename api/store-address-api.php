<?php

class StoreAddressAPI extends API
{
    private static $storeAddressAPI;
    protected static $validator;
    protected static $fileName = 'validate-address-fields.json';

    private function __construct() {}

    public static function getApi(): StoreAddressAPI
    {
        if (!isset(self::$storeAddressAPI))
            self::$storeAddressAPI = new self();

        if (!isset(self::$validator))
            self::$validator = AddressValidation::getValidator();

        return self::$storeAddressAPI;
    }

    public function get(array $args = []): void
    {
        $this->getMethodTemplate('store_address', $args);
    }

    public function post(): void
    {
        $this->postMethodTemplate(
            'store_address',
            [
                'store_id',
                'house_no',
                'street',
                'city',
                'region',
                'postal_code',
                'country'
            ]
        );
    }

    public function put(array $args): void
    {
        $this->putMethodTemplate(
            'store_address',
            $args,
            [
                'house_no',
                'street',
                'city',
                'region',
                'postal_code',
                'country'
            ]
        );
    }

    public function delete(array $args): void
    {
        $this->deleteMethodTemplate('store_address', $args);
    }
}
