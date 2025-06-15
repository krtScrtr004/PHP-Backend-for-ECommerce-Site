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
        $this->getMethodTemplate('user', $args);
    }

    public function post(): void
    {
        $this->postMethodTemplate(
            'user',
            [
                'id',
                'first_name',
                'last_name',
                'email',
                'password',
                'contact'
            ]
        );
    }

    public function put(array $args): void
    {
        $this->putMethodTemplate(
            'user',
            $args,
            [
                'first_name',
                'last_name',
                'email',
                'password',
                'profile_image_link',
                'contact'
            ]
        );
    }

    public function delete(array $args): void
    {
        $this->deleteMethodTemplate('user', $args);
    }
}
