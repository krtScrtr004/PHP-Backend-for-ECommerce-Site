<?php

class AddressAPI implements API
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
            self::$validator = AddrressValidation::getValidator();

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
                        $conditions[] = "$key = :$key";
                        $params[":$key"] = $value;
                    }
                    // Join conditions with AND in the WHERE clause
                    $stmt .= implode(' AND ', $conditions);
                } else {
                    Respond::respondFail($validateContents['message']);
                }

                $query = $conn->prepare($stmt);
                $query->execute($params);
                $result = $query->fetchAll();

                Logger::logAccess('Finieshed GET request on Address API.');
                Respond::respondSuccess(data: $result);
            }
        } catch (Exception $e) {
            Respond::respondException($e->getMessage());
        }
    }

    public function post(): void 
    {
        
    }

    public function put(array $args): void {}

    public function delete(array $args): void {}
}
