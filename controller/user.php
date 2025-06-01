<?php

class UserAPI
{
    private static $userAPI;

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
            $params = [];
            $stmt = 'SELECT * FROM user';

            if (count($args) > 0) {
                if (isset($args['id'])) {
                    $validateId = $this->validate(fieldName: 'id', data: $args['id'], callback: function ($param): array {
                        if (!is_numeric($param)) {
                            return [
                                'status' => false,
                                'message' => 'Id must be a number.'
                            ];
                        }
                        return ['status' => true];
                    });
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
            else if (preg_match("/[^a-zA-Z0-9_!@'-]/", $data) === 1) {
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

    private function sanitize(&$data): void
    {
        if (!$data)
            throw new ErrorException('No data array to sanitize.');

        if (isset($data['id'])) (int) $data['id'];
        if (isset($data['username'])) trim($data['username']);
        if (isset($data['email'])) filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        if (isset($data['password'])) trim($data['password']);
    }
}
