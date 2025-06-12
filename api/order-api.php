<?php

enum OrderStatus: string
{
    case P = 'pending';
    case S = 'shipped';
    case D = 'delivered';
    case C = 'cancelled';
}

class OrderAPI extends API
{
    private static $orderAPI;
    protected static $validator;
    protected static $fileName = 'validate-order-fields.json';

    private function __construct() {}

    public static function getApi(): OrderAPI
    {
        if (!isset(self::$orderAPI))
            self::$orderAPI = new self();

        if (!isset(self::$validator))
            self::$validator = OrderValidation::getValidator();

        return self::$orderAPI;
    }

    public function get(array $args = []): void 
    {
        $params = [
            'query' => 'SELECT * FROM orders',
            'args' => $args
        ];
        $this->getMethodTemplate($params);
    }

    public function post(): void 
    {
        $queryParams = [
            ':userId' => 'userId',
        ];

        $param = [
            'query' => 'INSERT INTO orders(user_id) VALUES(:userId)',
            'params' => $queryParams
        ];
        $this->postMethodTemplate($param);
    }

    public function put(array $args): void 
    {
        $queryParams = [
            ':id' => 'id',
            ':userId' => 'userId',
            'status' => 'status'
        ];

        $param = [
            'query' => 'UPDATE orders SET user_id = :userId, status = :status WHERE id = :id',
            'args' => $args,
            'params' => $queryParams
        ];
        $this->putMethodTemplate($param);
    }

    public function delete(array $args): void 
    {
        $params = [
            'query' => 'DELETE FROM orders WHERE id = :id',
            'args' => $args
        ];
        $this->deleteMethodTemplate($params);
    }
}
