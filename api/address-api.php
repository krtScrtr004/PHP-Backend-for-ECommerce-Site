<?php

/**
 * Address API
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
    private static $validator;
    private static $fileName = 'validate-address-fields.json';

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
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET')
                throw new LogicException('Bad Request.');

            Logger::logAccess('Create GET request on Address API.');

            $params = [];
            $stmt = 'SELECT * FROM user_address';

            if (count($args) > 0) {
                $stmt .= ' WHERE ';

                $validateContents = self::$validator->validateFields($args, self::$fileName);
                if ($validateContents['status']) {
                    // Collect conditions in an array
                    $conditions = [];
                    foreach ($args as $key => $value) {
                        $conditionKey = strtolower(camelToSnakeCase($key));
                        $conditions[] = "$conditionKey = :$key";
                        $params[":$key"] = $value;
                    }
                    // Join conditions with AND in the WHERE clause
                    $stmt .= implode(' AND ', $conditions);
                } else {
                    Respond::respondFail($validateContents['message']);
                }
            }

            $query = $conn->prepare($stmt);
            $query->execute($params);
            $result = $query->fetchAll();

            Logger::logAccess('Finieshed GET request on Address API.');
            Respond::respondSuccess(data: $result);
        } catch (Exception $e) {
            Respond::respondException($e->getMessage());
        }
    }

    public function post(): void
    {
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST')
                throw new LogicException('Bad Request.');

            Logger::logAccess('Create POST request on Addrres API.');

            $contents = decodeData('php://input');

            $validateContents = self::$validator->validateFields($contents, self::$fileName);
            if (!$validateContents['status'])
                Respond::respondFail($validateContents['message']);

            self::$validator->sanitize($contents);
            $params = [
                ':userId' => $contents['userId'],
                ':houseNo' => $contents['houseNo'],
                ':street' => $contents['street'],
                ':city' => $contents['city'],
                ':region' => $contents['region'],
                ':postalCode' => $contents['postalCode'],
                ':country' => $contents['country'],
            ];

            $stmt = 'INSERT INTO user_address(user_id, house_no, street, city, region, postal_code, country) VALUES(:userId, :houseNo, :street, :city, :region, :postalCode, :country)';
            $query = $conn->prepare($stmt);
            $query->execute($params);

            Logger::logAccess('Finished POST request on Address API.');
            Respond::respondSuccess('User address created successfully.', code: 201);
        } catch (Exception $e) {
            Respond::respondException($e->getMessage());
        }
    }

    public function put(array $args): void
    {
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT')
                throw new LogicException('Bad Request.');

            Logger::logAccess('Create PUT request on AAddress API.');


            $contents = decodeData('php://input');
            $mergedArrays = [...$args, ...$contents];

            $validateContents = self::$validator->validateFields($mergedArrays, self::$fileName);
            if (!$validateContents['status'])
                Respond::respondFail($validateContents['message']);

            self::$validator->sanitize($mergedArrays);
            $params = [
                ':userId' => $mergedArrays['userId'],
                ':houseNo' => $mergedArrays['houseNo'],
                ':street' => $mergedArrays['street'],
                ':city' => $mergedArrays['city'],
                ':region' => $mergedArrays['region'],
                ':postalCode' => $mergedArrays['postalCode'],
                ':country' => $mergedArrays['country'],
            ];

            $stmt = 'UPDATE user_address SET house_no = :houseNo, street = :street, city = :city, region = :region, postal_code = :postalCode, country = :country WHERE user_id = :userId';
            $query = $conn->prepare($stmt);
            $query->execute($params);

            Logger::logAccess('Finished PUT request on Address API.');
            Respond::respondSuccess('User address updated successfully.');
        } catch (Exception $e) {
            Respond::respondException($e->getMessage());
        }
    }

    public function delete(array $args): void
    {
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE')
                throw new LogicException('Bad request.');

            Logger::logAccess('Create DELETE request on User API.');

            $validateId = self::$validator->validateFields($args, self::$fileName);
            if (!$validateId['status'])
                Respond::respondFail($validateId['message']);

            self::$validator->sanitize($args);
            $params = [':userId' => $args['userId']];

            $stmt = 'DELETE FROM user_address WHERE user_id = :userId';
            $query = $conn->prepare($stmt);
            $query->execute($params);

            Logger::logAccess('Finished DELETE request on Address API.');
            Respond::respondSuccess('User address deleted successfully.');
        } catch (Exception $e) {
            Respond::respondException($e->getMessage());
        }
    }
}
