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
                    $validateId = $this->validateFields($args);
                    // Append WHERE clause to $stmt to filter user ID
                    if ($validateId['status']) {
                        $stmt .= ' WHERE id = :id';
                        $params[':id'] = $args['id'];
                    } else {
                        respondFail($validateId['message']);
                    }
                }

                $this->sanitize($args);
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

            $contents = decodeData('php://input');

            $validateContents = $this->validateFields($contents);
            if (!$validateContents['status'])
                respondFail($validateContents['message']);

            $this->sanitize($contents);
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

            $contents = decodeData('php://input');
            $mergedArrays = [...$args, ...$contents];

            $validateContents = $this->validateFields($mergedArrays);
            if (!$validateContents['status']) 
                respondFail($validateContents['message']);

            $this->sanitize($mergedArrays);
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

            $validateId = $this->validateFields($args);
            if (!$validateId['status'])
                respondFail($validateId['message']);

            $this->sanitize($args);
            $params = [':id' => $args['id']];

            $stmt = 'DELETE FROM user WHERE id = :id';
            $query = $conn->prepare($stmt);
            $query->execute($params);

            respondSuccess('User deleted successfully.');
        } catch (Exception $e) {
            respondException($e->getMessage());
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
        $allowedFieldNames = ["id", "first name", "last name", "email", "password", "contact"];
        if (!in_array($fieldName, $allowedFieldNames))
            throw new Exception("$fieldName is not a valid field name.");

        $fieldName = ucwords($fieldName);

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
        } else if (strcasecmp($fieldName, 'id') !== 0) {
            // Length requirement validation
            if (strlen($data) < $MIN || strlen($data) > $MAX) {
                $validationResult['status'] = false;
                $validationResult['message'] = "$fieldName must be between $MIN and $MAX only.";
            }

            /*
            * Allow only fields that are not in $exemptedFieldRegex 
            * Fields that are not allowed can have their own implementation of regex validation using the callback paramter
            */
            $exemptedFieldRegex = ["email", "password", "contact"];
            if (!in_array(strtolower($fieldName), $exemptedFieldRegex)) {
                // Allowed character validation
                if (preg_match('/[^a-zA-Z\s]+/', $data) === 1) {
                    $validationResult['status'] = false;
                    $validationResult['message'] = "$fieldName must only contain letters and spaces.";
                }
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

    // TODO: Test functionality
    private function validateFields(array $data): array
    {
        // Get all fields to validate
        $presentFields = array_keys($data);

        $dataPath = DATA_PATH . 'validateUserFields.json';
        if (!file_exists($dataPath)) {
            throw new ErrorException("$dataPath does not exists.");
        }

        $validateFields = decodeData($dataPath);
        foreach ($validateFields as $field) {
                // Skip field validation for fields that are not present
                if (!in_array($field['name'], $presentFields))
                    continue;

                $params = [
                    'fieldName' => $field['name'],
                    'data' => $data[$field['data']],
                ];
                if ($field['min'])
                    $params['MIN'] = $field['min'];
                if ($field['max'])
                    $params['MAX'] = $field['max'];
                if ($field['callback'])
                    $params['callback'] = [$this, $field['callback']];

                $validationResult = $this->validate(...$params);
                if (!$validationResult['status'])
                    return $validationResult;
        }
        return ['status' => true];
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

    private function validatePassword($param): array
    {
        if (preg_match("/[^a-zA-Z0-9_!@'.-]/", $param) === 1) {
            return [
                'status' => false,
                'message' => "Password should only contain lower and uppercase characters, numbers, and special characters (_, -, !, @, ')."
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

    private function validateContact($param): array
    {
        if (preg_match('/^0-9\[\]\-\_\(\)\+\s\#]+@/', $param) === 1) {
            return [
                'status' => false,
                'message' => 'Contact number should only contain numbers, space, and special characters (\+, \[, \], \(, \), \-, \_, \#).'
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
        if (isset($data['email'])) $data['email'] = filter_var($data['email'], FILTER_SANITIZE_EMAIL);

        $trimmableField = ['firstName', 'lastName', 'password', 'contact'];
        foreach ($trimmableField as $trimmable) {
            if (isset($data[$trimmable])) {
                $data[$trimmable] = trim($data[$trimmable]);
            }
        }
    }
}
