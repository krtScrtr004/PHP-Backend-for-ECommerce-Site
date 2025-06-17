<?php
/**
 * Class OrderItemApi
 *
 * Provides RESTful API operations for managing order items in the system.
 * Inherits from OrderAPI and implements CRUD (Create, Read, Update, Delete) methods
 * for the 'order_item' database table. Utilizes the singleton pattern to ensure a single
 * instance is used throughout the application.
 *
 * Main Methods:
 * - getApi(): Returns the singleton instance of OrderItemApi, initializing the validator if needed.
 * - get(array $args = []): Retrieves order item records, with optional query parameters.
 * - post(): Creates a new order item, supporting optional quantity.
 * - put(array $args): Updates an existing order item, allowing quantity modification.
 * - delete(array $args): Deletes an order item by identifier.
 *
 * Use this class to perform API operations such as listing, creating, updating, and deleting order items.
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
        $this->getMethodTemplate('order_item', $args);
    }

    public function post(): void
    {
        $columns = ['id', 'order_id', 'product_id'];
        $contents = decodeData('php://input');
        if (isset($contents['quantity']))
            array_push($columns, 'quantity');

        $this->postMethodTemplate(
            'order_item',
            $columns,
            $contents
        );
    }

    public function put(array $args): void
    {
        $this->putMethodTemplate(
            'order_item',
            $args,
            [
                'quantity'
            ]
        );
    }

    public function delete(array $args): void
    {
        $this->deleteMethodTemplate('order_item', $args);
    }
}
