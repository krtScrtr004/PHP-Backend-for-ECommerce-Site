<?php

/*
* UserAPI.php
* This file is part of the API project.
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

class UserAPI
{
    private static $userAPI; // Singleton Pattern

    private function __construct() {}

    /**
     * Summary of getApi
     * @return UserAPI
     */
    public static function getApi()
    {
        if (!isset(self::$userAPI))
            self::$userAPI = new self();

        return self::$userAPI;
    }

    /**
     * Summary of get
     * @param array $args
     * @throws \LogicException
     * @return never
     */
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
                    $validateId = $this->validate(fieldName: 'id', data: $args['id'], callback: [$this, 'validateId']);
                    // Append WHERE clause to $stmt to filter user ID
                    if ($validateId['status']) {
                        $stmt .= ' WHERE id = :id';
                        $params[':id'] = $args['id'];
                    } else {
                        respond(status: 'error', message: $validateId['message'], code: 400);
                    }
                }

                $this->sanitize($args);
            }


            $query = $conn->prepare($stmt);
            $query->execute($params);
            $result = $query->fetchAll();

            // return $query->fetchAll();
            respond(status: 'success', data: $result, code: 200);
        } catch (Exception $e) {
            respond(status: 'exception', message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Summary of post
     * @throws \LogicException
     * @return never
     */
    public function post(): void
    {
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST')
                throw new LogicException('Bad request.');

            $contents = decodeData(file_get_contents('php://input'));

            $validateUsername = $this->validate(fieldName: 'username', data: $contents['username'], MIN: 2);
            if (!$validateUsername['status'])
                respond(status: 'fail', message: $validateUsername['message'], code: 400);

            $validateEmail = $this->validate(fieldName: 'email', data: $contents['email'], callback: [$this, 'validateEmail']);
            if (!$validateEmail['status'])
                respond(status: 'fail', message: $validateEmail['message'], code: 400);

            $validatePassword = $this->validate(fieldName: 'password', data: $contents['password']);
            if (!$validatePassword['status'])
                respond(status: 'fail', message: $validatePassword['message'], code: 400);

            $this->sanitize($contents);
            $params = [
                ':username'  => $contents['username'],
                ':email'  => $contents['email'],
                ':password'  => password_hash($contents['password'], PASSWORD_ARGON2ID),
            ];

            $stmt = 'INSERT INTO user(username, email, password) VALUES(:username, :email, :password)';
            $query = $conn->prepare($stmt);
            $query->execute($params);

            respond(status: 'success', message: 'User created successfully.', code: 201);
        } catch (Exception $e) {
            respond(status: 'exception', message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Summary of put
     * @param array $args
     * @throws \LogicException
     * @return never
     */
    public function put(array $args = []): void
    {
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT')
                throw new LogicException('Bad request.');

            $validateId = $this->validate(fieldName: 'id', data: $args['id'] ?? '', callback: [$this, 'validateId']);
            if (!$validateId['status'])
                respond(status: 'error', message: $validateId['message'], code: 400);

            $contents = decodeData(file_get_contents('php://input'));

            $validateUsername = $this->validate(fieldName: 'username', data: $contents['username'], MIN: 2);
            if (!$validateUsername['status'])
                respond(status: 'error', message: $validateUsername['message'], code: 400);

            $validateEmail = $this->validate(fieldName: 'email', data: $contents['email'], callback: [$this, 'validateEmail']);
            if (!$validateEmail['status'])
                respond(status: 'error', message: $validateEmail['message'], code: 400);

            $validatePassword = $this->validate(fieldName: 'password', data: $contents['password']);
            if (!$validatePassword['status'])
                respond(status: 'error', message: $validatePassword['message'], code: 400);

            $mergedArrays = [...$args, ...$contents];
            $this->sanitize($mergedArrays);
            $params = [
                ':id' => $mergedArrays['id'],
                ':username' => $mergedArrays['username'],
                ':email' => $mergedArrays['email'],
                ':password' => password_hash($mergedArrays['password'], PASSWORD_ARGON2ID)
            ];

            $stmt = 'UPDATE user SET username = :username, email = :email, password = :password WHERE id = :id';
            $query = $conn->prepare($stmt);
            $query->execute($params);

            respond(status: 'success', message: 'User updated successfully.', code: 200);
        } catch (Exception $e) {
            respond(status: 'exception', message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Summary of delete
     * @param array $args
     * @throws \LogicException
     * @return never
     */
    public function delete(array $args = []): void
    {
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE')
                throw new LogicException('Bad request.');

            $validateId = $this->validate(fieldName: 'id', data: $args['id'] ?? '', callback: [$this, 'validateId']);
            if (!$validateId['status'])
                respond(status: 'error', message: $validateId['message'], code: 400);

            $this->sanitize($args);
            $params = [':id' => $args['id']];

            $stmt = 'DELETE FROM user WHERE id = :id';
            $query = $conn->prepare($stmt);
            $query->execute($params);

            respond(status: 'success', message: 'User deleted successfully.', code: 200);
        } catch (Exception $e) {
            respond(status: 'exception', message: $e->getMessage(), code: 500);
        }
    }

    /* ------------------------------------------------------------------------------------------*/

    /**
     * Summary of validate
     * @param string $fieldName
     * @param mixed $data
     * @param int $MIN
     * @param int $MAX
     * @param mixed $callback
     * @throws \Exception
     * @return array
     */
    private function validate(string $fieldName, mixed $data, int $MIN = 8, int $MAX = 255, ?callable $callback = null): array
    {
        $allowedFieldNames = ["id", "username", "email", "password"];
        if (!in_array($fieldName, $allowedFieldNames))
            throw new Exception("$fieldName is not a valid field name.");

        $fieldName = ucfirst($fieldName);

        $validationResult = [
            'status' => true,
            'message' => "$fieldName is valid."
        ];

        // Undefined / Null validation
        if (!$data) {
            $validationResult['status'] = false;
            $validationResult['message'] = "$fieldName is not defined";
        }
        // Empty string validation
        else if (empty($data)) {
            $validationResult['status'] = false;
            $validationResult['message'] = "$fieldName cannot be empty.";
        } else if (strcasecmp($fieldName, 'id')) {
            // Length requirement validation
            if (strlen($data) < $MIN || strlen($data) > $MAX) {
                $validationResult['status'] = false;
                $validationResult['message'] = "$fieldName must be between $MIN and $MAX only.";
            }
            // Allowed character validation
            else if (preg_match("/[^a-zA-Z0-9_!@'.-]/", $data) === 1) {
                $validationResult['status'] = false;
                $validationResult['message'] = "$fieldName should only contain lower and uppercase characters, numbers, and special characters (_, -, !, @, ').";
            }
        }
        // Callback function is defined
        else if ($callback && is_callable($callback)) {
            $callbackReturn = call_user_func($callback, $data);
            if (!$callbackReturn['status'])
                $validationResult = $callbackReturn;
        }

        return $validationResult;
    }

    /* --Callback validator functions-- */

    /**
     * Summary of validateId
     * @param mixed $param
     * @return array{message: string, status: bool|array{status: bool}}
     */
    private function validateId($param): array
    {
        if (!is_numeric($param)) {
            return [
                'status' => false,
                'message' => 'Id must be a number.'
            ];
        }
        return ['status' => true];
    }

    /**
     * Summary of validateEmail
     * @param mixed $param
     * @return array{message: string, status: bool|array{status: bool}}
     */
    private function validateEmail($param): array
    {
        if (!filter_var($param, FILTER_VALIDATE_EMAIL)) {
            return [
                'status' => false,
                'message' => 'Invalid email format.'
            ];
        }
        return ['status' => true];
    }

    /**
     * Summary of sanitize
     * @param mixed $data
     * @throws \ErrorException
     * @return void
     */
    private function sanitize(&$data): void
    {
        if (!$data)
            throw new ErrorException('No data array to sanitize.');

        if (isset($data['id'])) $data['id'] = (int) $data['id'];
        if (isset($data['username'])) $data['username'] = trim($data['username']);
        if (isset($data['email'])) $data['email'] = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        if (isset($data['password'])) $data['password'] = trim($data['password']);
    }
}
