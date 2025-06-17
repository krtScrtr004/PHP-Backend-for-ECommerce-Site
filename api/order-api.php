<?php

/**
 * Class OrderAPI
 *
 * Provides a RESTful API for managing orders.
 * 
 * Responsibilities:
 * - Handles HTTP GET, POST, PUT, and DELETE requests for orders.
 * - Validates order data using OrderValidation.
 * - Interacts with the database for CRUD operations on the "order" table.
 * - Uses a singleton pattern for consistent API access.
 * 
 * Main Methods:
 * - getApi(): Returns the singleton instance and initializes the validator.
 * - get(array $args = []): Retrieves orders, with optional filters.
 * - post(): Creates a new order, setting expected arrival to 3 days from now.
 * - put(array $args): Updates an order's status and actual arrival.
 * - delete(array $args): Deletes an order by ID.
 *
 * Usage:
 * Use OrderAPI::getApi() to obtain the instance, then call methods based on HTTP requests.
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
        $this->getMethodTemplate('order', $args);
    }

    public function post(): void
    {
        $currentDateTime = new DateTime();
        $currentDateTime->add(new DateInterval('P3D'));

        $contents = decodeData('php://input');
        $contents['expected_arrival'] = $currentDateTime->format("Y-m-d H:i:s");

        $this->postMethodTemplate('order', ['id', 'user_id', 'expected_arrival'], $contents);
    }

    public function put(array $args): void
    {
        $this->putMethodTemplate(
            'order',
            $args,
            [ 
                'status',
                'actual_arrival'
            ]
        );
    }

    public function delete(array $args): void
    {
        $this->deleteMethodTemplate('order', $args);
    }
}
