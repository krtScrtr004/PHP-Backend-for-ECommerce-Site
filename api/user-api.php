<?php

/**
 * Class UserAPI
 *
 * UserAPI provides a RESTful interface for managing user records in the database.
 * It extends the base API class and implements CRUD operations (Create, Read, Update, Delete)
 * for users. The class uses the Singleton pattern to ensure a single instance is used
 * throughout the application. User data validation is handled via the UserValidation class.
 *
 * Main Features:
 * - Singleton access via getApi()
 * - GET: Retrieve user records with optional query arguments
 * - POST: Create a new user with validated and hashed data
 * - PUT: Update existing user records, including password hashing and field validation
 * - DELETE: Remove user records by ID or other criteria
 *
 * Intended Usage:
 * Use this class to expose user management endpoints in a RESTful API.
 * Each method corresponds to an HTTP verb and interacts with the user table accordingly.
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
                'contact',
                'is_verified'
            ]
        );
    }

    public function delete(array $args): void
    {
        $this->deleteMethodTemplate('user', $args);
    }
}
