<?php

class OrderItemApi extends OrderAPI
{
    private static $orderItemAPI;

    private function __construct() {}

    public static function getApi(): OrderAPI
    {
        if (!isset(self::$orderItemAPI))
            self::$orderItemAPI = new self();

        if (!isset(static::$validator))
            static::$validator = OrderValidation::getValidator();

        return self::$orderItemAPI;
    }

    public function get(array $args = []): void 
    {
        $params = [
            'query' => 'SELECT * FROM order_item',
            'args' => $args
        ];
        $this->getMethodTemplate($params);
    }

    public function post(): void 
    {
        $query = 'INSERT INTO order_item(order_id, product_id) VALUES(:orderId, :productId)';

        $contents = decodeData('php://input');
        $queryParams = [
            ':orderId' => 'orderId',
            ':productId' => 'productId'
        ];
        if (isset($contents['quantity'])) {
            $queryParams[':quantity'] = 'quantity';

            // Update query to include quantity
            $query = 'INSERT INTO order_item(order_id,product_id, quantity) VALUES(:orderId, :productId, :quantity)';
        }

        $param = [
            'query' => $query,
            'params' => $queryParams,
            'contents' => $contents
        ];
        $this->postMethodTemplate($param);
    }

    public function put(array $args): void 
    {
        $queryParams = [
            ':id' => 'id',
            ':orderId' => 'orderId',
            ':productId' => 'productId',
            ':quantity' => 'quantity'
        ];

        $param = [
            'query' => 'UPDATE order_item SET order_id = :orderId, product_id = :productId, quantity = :quantity WHERE id = :id',
            'args' => $args,
            'params' => $queryParams
        ];
        $this->putMethodTemplate($param);
    }

    public function delete(array $args): void 
    {
        $params = [
            'query' => 'DELETE FROM order_item WHERE id = :id',
            'args' => $args
        ];
        $this->deleteMethodTemplate($params);
    }
}
