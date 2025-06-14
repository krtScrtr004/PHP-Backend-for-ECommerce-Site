<?php

/**
 * Class AddressAPI
 *
 * The AddressAPI class provides a RESTful API interface for managing user address records in a database.
 * It extends the base API class and implements CRUD (Create, Read, Update, Delete) operations for the `user_address` table.
 * 
 * This class uses the singleton pattern to ensure only one instance is created and manages address validation via a validator.
 * 
 * Methods:
 * - getApi(): Returns the singleton instance of AddressAPI and initializes the address validator if not already set.
 * - get(array $args = []): Handles HTTP GET requests to retrieve address records from the database. Accepts optional query arguments.
 * - post(): Handles HTTP POST requests to create a new address record. Reads input data, prepares parameters, and inserts a new row.
 * - put(array $args): Handles HTTP PUT requests to update an existing address record. Merges URL and input data, then updates the corresponding row.
 * - delete(array $args): Handles HTTP DELETE requests to remove an address record by its ID.
 *
 * Usage:
 * Instantiate and use this class through the getApi() static method. Each public method corresponds to a RESTful endpoint for address management.
 */

class AddressAPI extends API
{
    private static $addressAPI;
    protected static $validator;
    protected static $fileName = 'validate-address-fields.json';

    private function __construct() {}

    public static function getApi(): AddressAPI
    {
        if (!isset(self::$addressAPI))
            self::$addressAPI = new self();

        if (!isset(self::$validator))
            self::$validator = AddressValidation::getValidator();

        return self::$addressAPI;
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
                'steet',
                'city',
                'region',
                'postal_code',
                'country'
            ]
        );
    }

    public function put(array $args): void
    {
        $this->postMethodTemplate(
            'user_address',
            $args,
            [
                'user_id',
                'house_no',
                'steet',
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
