<?php

/**
 * Class StoreAddressAPI
 *
 * Handles CRUD operations for store address resources via API endpoints.
 * Implements singleton pattern to ensure a single instance and manages address validation.
 *
 * Methods:
 * - getApi(): Returns the singleton instance of StoreAddressAPI and initializes the address validator.
 * - get(array $args = []): Handles GET requests for retrieving store address data. Accepts optional filter arguments.
 * - post(): Handles POST requests for creating a new store address. Expects required address fields in the request.
 * - put(array $args): Handles PUT requests for updating an existing store address. Requires address fields and filter arguments.
 * - delete(array $args): Handles DELETE requests for removing a store address. Requires filter arguments to identify the record.
 *
 * Utilizes method templates (getMethodTemplate, postMethodTemplate, putMethodTemplate, deleteMethodTemplate)
 * for consistent API behavior and validation.
 */

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
