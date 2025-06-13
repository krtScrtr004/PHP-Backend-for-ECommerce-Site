<?php

/**
 * Class UserAPI
 *
 * The UserAPI class provides a RESTful API interface for managing user data.
 * It extends the base API class and implements CRUD operations (Create, Read, Update, Delete)
 * for user records in the database. This class uses the Singleton pattern to ensure only one
 * instance is used throughout the application. It also integrates user data validation via
 * the UserValidation class.
 *
 * Methods:
 * - getApi(): Returns the singleton instance of UserAPI and initializes the validator if needed.
 * - get(array $args = []): Retrieves user records from the database. Accepts optional arguments for query customization.
 * - post(): Handles the creation of a new user. Reads input data, hashes the password, and inserts a new user record.
 * - put(array $args): Updates an existing user record. Merges input data and arguments, hashes the password, and updates the user in the database.
 * - delete(array $args): Deletes a user record based on the provided arguments (typically user ID).
 *
 * Usage:
 * Use this class to expose user management endpoints in a RESTful API. Each method corresponds to an HTTP verb
 * (GET, POST, PUT, DELETE) and interacts with the user table in the database accordingly.
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
        $query = ['table' => 'user'];
        $params = [
            'query' => $query,
            'args' => $args
        ];
        
        $this->getMethodTemplate($params);
    }

    public function post(): void
    {
        $queryParams = [
            ':firstName'  => 'firstName',
            ':lastName' => 'lastName',
            ':email'  => 'email',
            ':password'  => 'password',
            ':contact' => 'contact'
        ];

        $param = [
            'query' => 'INSERT INTO user(first_name, last_name, email, password, contact) VALUES(:firstName, :lastName, :email, :password, :contact)',
            'params' => $queryParams
        ];
        $this->postMethodTemplate($param);
    }

    public function put(array $args): void
    {
        $queryParams = [
            ':id' => 'id',
            ':firstName' => 'firstName',
            ':lastName' => 'lastName',
            ':email' => 'email',
            ':password' => 'password',
            ':contact' => 'contact'
        ];

        $param = [
            'query' => 'UPDATE user SET first_name = :firstName, last_name = :lastName, email = :email, password = :password, contact = :contact WHERE id = :id',
            'args' => $args,
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
