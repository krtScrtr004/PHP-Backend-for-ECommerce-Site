<?php

/**
 * 
 * Address API class
 *
 * This file provides API endpoints and logic for managing user addresses.
 * It handles operations such as creating, retrieving, updating,
 * and deleting addresses associated with users in the system.
 *
 * Usage:
 * - get(array $args = []): Retrieve user addresses, optionally filtered by parameters.
 * - post(): Create a new user address.
 * - put(array $args): Update an existing user address.
 * - delete(array $args): Delete a user address.
 *
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
        $params = [
            'query' => 'SELECT * FROM user_address',
            'args' => $args
        ];
        $this->getMethodTemplate($params);
    }

    public function post(): void
    {
        $contents = decodeData('php://input');
        $queryParams = [
            ':userId' => $contents['userId'],
            ':houseNo' => $contents['houseNo'],
            ':street' => $contents['street'],
            ':city' => $contents['city'],
            ':region' => $contents['region'],
            ':postalCode' => $contents['postalCode'],
            ':country' => $contents['country'],
        ];

        $param = [
            'query' => 'INSERT INTO user_address(user_id, house_no, street, city, region, postal_code, country) VALUES(:userId, :houseNo, :street, :city, :region, :postalCode, :country)',
            'contents' => $contents,
            'params' => $queryParams
        ];
        $this->postMethodTemplate($param);
    }

    public function put(array $args): void
    {
        $mergedArrays = [...$args, ...decodeData('php://input')];
        $queryParams = [
            ':userId' => $mergedArrays['userId'],
            ':houseNo' => $mergedArrays['houseNo'],
            ':street' => $mergedArrays['street'],
            ':city' => $mergedArrays['city'],
            ':region' => $mergedArrays['region'],
            ':postalCode' => $mergedArrays['postalCode'],
            ':country' => $mergedArrays['country'],
        ];

        $param = [
            'query' => 'UPDATE user_address SET house_no = :houseNo, street = :street, city = :city, region = :region, postal_code = :postalCode, country = :country WHERE user_id = :userId',
            'contents' => $mergedArrays,
            'params' => $queryParams
        ];
        $this->putMethodTemplate($param);
    }

    public function delete(array $args): void
    {
        $params = [
            'query' => 'DELETE FROM user_address WHERE id = :id',
            'args' => $args
        ];
        $this->deleteMethodTemplate($params);
    }
}
