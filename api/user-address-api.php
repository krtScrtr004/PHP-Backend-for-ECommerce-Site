<?php

/**
 * Class UserAddressAPI
 *
 * Provides a RESTful API for managing user address records in the `user_address` database table.
 * Inherits from the base API class and implements CRUD operations (Create, Read, Update, Delete).
 *
 * Features:
 * - Singleton pattern: Ensures only one instance of the API is used.
 * - Field validation: Uses an address validator for input validation.
 * - RESTful endpoints: Methods correspond to HTTP verbs for address management.
 *
 * Methods:
 * - getApi(): Returns the singleton instance and initializes the validator if needed.
 * - get(array $args = []): Handles GET requests to retrieve address records, with optional filters.
 * - post(): Handles POST requests to create a new address record.
 * - put(array $args): Handles PUT requests to update an existing address record.
 * - delete(array $args): Handles DELETE requests to remove an address record by ID.
 *
 * Usage:
 * Use UserAddressAPI::getApi() to obtain the instance and call the appropriate method for each RESTful operation.
 */

class UserAddressAPI extends API
{
    private static $userAddressAPI;
    protected static $validator;
    protected static $fileName = 'validate-address-fields.json';

    private function __construct() {}

    public static function getApi(): UserAddressAPI
    {
        if (!isset(self::$userAddressAPI))
            self::$userAddressAPI = new self();

        if (!isset(self::$validator))
            self::$validator = AddressValidation::getValidator();

        return self::$userAddressAPI;
    }

    public function get(array $args = []): void
    {
        $this->getMethodTemplate('user_address', $args);
    }

    public function post(): void
    {
        $this->postMethodTemplate(
            'user_address',
            [
                'user_id',
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
            'user_address',
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
        $this->deleteMethodTemplate('user_address', $args);
    }
}
