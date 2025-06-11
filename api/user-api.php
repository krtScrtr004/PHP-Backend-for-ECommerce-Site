<?php

/**
 * User API
 *
 * This file provides API endpoints and logic for managing users.
 * It handles operations such as creating, retrieving, updating,
 * and deleting users in the system.
 *
 * Usage:
 * - get(array $args = []): Retrieve users, optionally filtered by parameters.
 * - post(): Create a new user.
 * - put(array $args): Update an existing user.
 * - delete(array $args): Delete a user.
 *
 */

class UserAPI extends API
{
    private static $userAPI; // Singleton Pattern
    protected static $validator;
    protected static $fileName = 'validate-user-fields.json';

    private function __construct() {}

    public static function getApi(): UserAPI
    {
        if (!isset(self::$userAPI))
            self::$userAPI = new self();

        if (!isset(self::$validator))
            self::$validator = UserValidation::getValidator();

        return self::$userAPI;
    }

    public function get(array $args = []): void
    {
        $params = [
            'query' => 'SELECT * FROM user',
            'args' => $args
        ];
        $this->getMethodTemplate($params);
    }

    public function post(): void
    {
        $contents = decodeData('php://input');
        $queryParams = [
            ':firstName'  => $contents['firstName'],
            ':lastName' => $contents['lastName'],
            ':email'  => $contents['email'],
            ':password'  => password_hash($contents['password'], PASSWORD_ARGON2ID),
            ':contact' => $contents['contact']
        ];

        $param = [
            'query' => 'INSERT INTO user(first_name, last_name, email, password, contact) VALUES(:firstName, :lastName, :email, :password, :contact)',
            'contents' => $contents,
            'params' => $queryParams
        ];
        $this->postMethodTemplate($param);
    }

    public function put(array $args): void
    {
        $mergedArrays = [...$args, ...decodeData('php://input')];
        $queryParams = [
            ':id' => $mergedArrays['id'],
            ':firstName' => $mergedArrays['firstName'],
            ':lastName' => $mergedArrays['lastName'],
            ':email' => $mergedArrays['email'],
            ':password' => password_hash($mergedArrays['password'], PASSWORD_ARGON2ID),
            ':contact' => $mergedArrays['contact']
        ];

        $param = [
            'query' => 'UPDATE user SET first_name = :firstName, last_name = :lastName, email = :email, password = :password, contact = :contact WHERE id = :id',
            'contents' => $mergedArrays,
            'params' => $queryParams
        ];
        $this->putMethodTemplate($param);
    }

    public function delete(array $args): void
    {
        $params = [
            'query' => 'DELETE FROM user WHERE id = :id',
            'args' => $args
        ];
        $this->deleteMethodTemplate($params);
    }
}
