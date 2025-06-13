<?php

/**
 * Class OrderAPI
 *
 * The OrderAPI class provides a RESTful API interface for managing orders in the system.
 * It extends the base API class and implements CRUD operations for the "orders" resource.
 * This class uses a singleton pattern to ensure only one instance is used throughout the application.
 * 
 * Main responsibilities:
 * - Handling HTTP GET, POST, PUT, and DELETE requests for orders.
 * - Validating order data using an external OrderValidation class.
 * - Interacting with the database to perform operations on the "orders" table.
 * 
 * Methods:
 * - getApi(): Returns the singleton instance of OrderAPI and initializes the validator if needed.
 * - get(array $args = []): Retrieves orders from the database. Accepts optional filtering arguments.
 * - post(): Creates a new order in the database. Expects user ID as input.
 * - put(array $args): Updates an existing order's user ID and status based on provided arguments.
 * - delete(array $args): Deletes an order from the database by its ID.
 *
 * Usage:
 * Instantiate the class using OrderAPI::getApi() and call the appropriate method based on the HTTP request type.
 */

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
        $this->getMethodTemplate([
            'table' => 'orders',
            'args' => $args
        ]);
    }

    public function post(): void
    {
        $this->postMethodTemplate([
            'table' => 'orders',
            'columns' => ['user_id']
        ]);
    }

    public function put(array $args): void
    {
        $this->putMethodTemplate([
            'table' => 'orders',
            'args' => $args,
            'columns' => ['user_id', 'status']
        ]);
    }

    public function delete(array $args): void
    {
        $this->deleteMethodTemplate([
            'table' => 'order',
            'args' => $args
        ]);
    }
}
