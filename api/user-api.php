<?php

/*
* UserAPI.php
*
* This file contains the UserAPI class which handles user-related API requests.
* It supports CRUD operations for user data.

*
* Usage:
* $userAPI = UserAPI::getApi();
* $userAPI->get(); // Fetch user
* $userAPI->post(); // Create a new user
* $userAPI->put(); // Update user
* $userAPI->delete(); // Delete user
*
*/

class UserAPI implements API
{
    private static $userAPI; // Singleton Pattern
    private static $validator;
    private static $fileName = 'validate-user-fields.json';

    private function __construct() {}

    public static function getApi()
    {
        if (!isset(self::$userAPI))
            self::$userAPI = new self();

        if (!isset(self::$validator))
            self::$validator = UserValidation::getValidator();

        return self::$userAPI;
    }

    public function get(array $args = []): void
    {
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET')
                throw new LogicException('Bad request.');

            Logger::logAccess('Create GET request on User API.');

            $params = [];
            $stmt = 'SELECT * FROM user';

            // Append WHERE clause if query strings is / are present
            if (count($args) > 0) {
                $stmt .= ' WHERE ';

                $validateContents = self::$validator->validateFields($args, self::$fileName);
                if ($validateContents['status']) {
                    self::$validator->sanitize($args);

                    // Collect conditions in an array
                    $conditions = [];
                    foreach ($args as $key => $value) {
                        $conditions[] = "$key = :$key";
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

            Logger::logAccess('Finished GET request on User API.');
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
                throw new LogicException('Bad request.');

            Logger::logAccess('Create POST request on User API.');

            $contents = decodeData('php://input');

            $validateContents = self::$validator->validateFields($contents, self::$fileName);
            if (!$validateContents['status'])
                Respond::respondFail($validateContents['message']);

            self::$validator->sanitize($contents);
            $params = [
                ':firstName'  => $contents['firstName'],
                ':lastName' => $contents['lastName'],
                ':email'  => $contents['email'],
                ':password'  => password_hash($contents['password'], PASSWORD_ARGON2ID),
                ':contact' => $contents['contact']
            ];

            $stmt = 'INSERT INTO user(first_name, last_name, email, password, contact) VALUES(:firstName, :lastName, :email, :password, :contact)';
            $query = $conn->prepare($stmt);
            $query->execute($params);

            Logger::logAccess('Finished POST request on User API.');
            Respond::respondSuccess('User created successfully.', code: 201);
        } catch (Exception $e) {
            Respond::respondException($e->getMessage());
        }
    }

    public function put(array $args): void
    {
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT')
                throw new LogicException('Bad request.');

            Logger::logAccess('Create PUT request on User API.');

            $contents = decodeData('php://input');
            $mergedArrays = [...$args, ...$contents];

            $validateContents = self::$validator->validateFields($mergedArrays, self::$fileName);
            if (!$validateContents['status'])
                Respond::respondFail($validateContents['message']);

            self::$validator->sanitize($mergedArrays);
            $params = [
                ':id' => $mergedArrays['id'],
                ':firstName' => $mergedArrays['firstName'],
                ':lastName' => $mergedArrays['lastName'],
                ':email' => $mergedArrays['email'],
                ':password' => password_hash($mergedArrays['password'], PASSWORD_ARGON2ID),
                ':contact' => $mergedArrays['contact']
            ];

            $stmt = 'UPDATE user SET first_name = :firstName, last_name = :lastName, email = :email, password = :password, contact = :contact WHERE id = :id';
            $query = $conn->prepare($stmt);
            $query->execute($params);

            Logger::logAccess('Finished PUT request on User API.');
            Respond::respondSuccess('User updated successfully.');
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
            $params = [':id' => $args['id']];

            $stmt = 'DELETE FROM user WHERE id = :id';
            $query = $conn->prepare($stmt);
            $query->execute($params);

            Logger::logAccess('Finished DELETE request on User API.');
            Respond::respondSuccess('User deleted successfully.');
        } catch (Exception $e) {
            Respond::respondException($e->getMessage());
        }
    }
}
