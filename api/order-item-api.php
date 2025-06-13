<?php

/**
 * Class OrderItemApi
 *
 * Handles API operations related to order items in the system.
 * This class extends OrderAPI and provides CRUD (Create, Read, Update, Delete) methods
 * for managing order items in the database. It implements the singleton pattern to ensure
 * only one instance is used throughout the application.
 *
 * Methods:
 * - getApi(): Returns the singleton instance of OrderItemApi, initializing it and its validator if necessary.
 * - get(array $args = []): Retrieves order item records from the database. Accepts optional arguments for query customization.
 * - post(): Creates a new order item record in the database. Reads input data, supports optional quantity, and inserts the record.
 * - put(array $args): Updates an existing order item record in the database. Requires identifiers and new values as arguments.
 * - delete(array $args): Deletes an order item record from the database based on the provided identifier.
 *
 * Usage:
 * Use this class to perform RESTful API operations on order items, such as listing, creating, updating, and deleting order items.
 */

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
