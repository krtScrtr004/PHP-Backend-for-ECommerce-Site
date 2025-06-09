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
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST')
                throw new LogicException('Bad Request.');

            Logger::logAccess('Create POST request on Addrres API.');

            $contents = decodeData('php://input');

            $validateContents = self::$validator->validateFields($contents, self::$fileName);
            if (!$validateContents['status'])
                Respond::respondFail($validateContents['message']);

            self::$validator->sanitize($contents);
            $params = [
                ':userId' => $contents['userId'],
                ':houseNo' => $contents['houseNo'],
                ':street' => $contents['street'],
                ':city' => $contents['city'],
                ':region' => $contents['region'],
                ':postalCode' => $contents['postalCode'],
                ':country' => $contents['country'],
            ];

            $stmt = 'INSERT INTO user_address(user_id, house_no, street, city, region, postal_code, country) VALUES(:userId, :houseNo, :street, :city, :region, :postalCode, :country)';
            $query = $conn->prepare($stmt);
            $query->execute($params);
            
            Logger::logAccess('Finished POST request on Address API.');
            Respond::respondSuccess('User address created successfully.', code: 201);
        } catch (Exception $e) {
            Respond::respondException($e->getMessage());
        }
    }

    public function put(array $args): void {}

    public function delete(array $args): void {}
}
