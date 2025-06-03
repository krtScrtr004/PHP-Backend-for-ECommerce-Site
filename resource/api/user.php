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

require_once IMPLMENTAION_PATH . 'userValidation.php';
class UserAPI
{
    private static $userAPI; // Singleton Pattern
    private static $validator;

    private function __construct() {}

    public static function getApi()
    {
        if (!isset(self::$userAPI))
            self::$userAPI = new self();

        return self::$userAPI;
    }

    public function get(array $args = []): void
    {
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET')
                throw new LogicException('Bad request.');

            $params = [];
            $stmt = 'SELECT * FROM user';

            if (count($args) > 0) {
                if (isset($args['id'])) {
                    $validateId = userValidation::validateFields($args);
                    // Append WHERE clause to $stmt to filter user ID
                    if ($validateId['status']) {
                        $stmt .= ' WHERE id = :id';
                        $params[':id'] = $args['id'];
                    } else {
                        respondFail($validateId['message']);
                    }
                }

                userValidation::sanitize($args);
            }


            $query = $conn->prepare($stmt);
            $query->execute($params);
            $result = $query->fetchAll();

            // return $query->fetchAll();
            respondSuccess(data: $result);
        } catch (Exception $e) {
            respondException($e->getMessage());
        }
    }

    public function post(): void
    {
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST')
                throw new LogicException('Bad request.');

            $contents = decodeData('php://input');

            $validateContents = userValidation::validateFields($contents);
            if (!$validateContents['status'])
                respondFail($validateContents['message']);

            userValidation::sanitize($contents);
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

            respondSuccess('User created successfully.', code: 201);
        } catch (Exception $e) {
            respondException($e->getMessage());
        }
    }

    public function put(array $args = []): void
    {
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT')
                throw new LogicException('Bad request.');

            $contents = decodeData('php://input');
            $mergedArrays = [...$args, ...$contents];

            $validateContents = userValidation::validateFields($mergedArrays);
            if (!$validateContents['status']) 
                respondFail($validateContents['message']);

            userValidation::sanitize($mergedArrays);
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

            respondSuccess('User updated successfully.');
        } catch (Exception $e) {
            respondException($e->getMessage());
        }
    }

    public function delete(array $args = []): void
    {
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE')
                throw new LogicException('Bad request.');

            $validateId = userValidation::validateFields($args);
            if (!$validateId['status'])
                respondFail($validateId['message']);

            userValidation::sanitize($args);
            $params = [':id' => $args['id']];

            $stmt = 'DELETE FROM user WHERE id = :id';
            $query = $conn->prepare($stmt);
            $query->execute($params);

            respondSuccess('User deleted successfully.');
        } catch (Exception $e) {
            respondException($e->getMessage());
        }
    }
}
