<?php

/**
 * Class ProductAPI
 *
 * The ProductAPI class provides a RESTful API interface for managing products in a database.
 * It extends the base API class and implements CRUD (Create, Read, Update, Delete) operations
 * for the 'product' resource. This class follows the singleton pattern to ensure only one instance
 * is used throughout the application.
 *
 * Responsibilities:
 * - Handles HTTP GET, POST, PUT, and DELETE requests for products.
 * - Validates product data using a validator loaded from a JSON schema.
 * - Interacts with the database to perform operations on the 'product' table.
 *
 * Methods:
 * - getApi(): Returns the singleton instance of ProductAPI and ensures the validator is set.
 * - get(array $args = []): Retrieves all products from the database. Accepts optional arguments for filtering.
 * - post(): Creates a new product using data from the request body. Validates and inserts the product into the database.
 * - put(array $args): Updates an existing product. Merges URL arguments and request body, then updates the product in the database.
 * - delete(array $args): Deletes a product specified by its ID from the database.
 *
 * Usage:
 * Instantiate and use this class via the getApi() static method. Use the public methods to handle
 * corresponding HTTP requests for product resources.
 */

class ProductAPI extends API
{
    private static $productAPI;
    protected static $validator;
    protected static $fileName = 'validate-product-fields.json';

    protected function __construct() {}


    protected static function setValidator(): void
    {
        if (!isset(self::$validator))
            self::$validator = ProductValidation::getValidator();
    }

    public static function getApi(): ProductAPI
    {
        if (!isset(self::$productAPI))
            self::$productAPI = new self();

        self::setValidator();

        return self::$productAPI;
    }

    public function get(array $args = []): void
    {
        $params = [
            'query' => 'SELECT * FROM product',
            'args' => $args
        ];
        $this->getMethodTemplate($params);
    }

    public function post(): void
    {
        $queryParams = [
            ':name' => 'name',
            ':description' => 'description',
            ':price' => 'price'
        ];

        $param = [
            'query' => 'INSERT INTO product(name, description, price) VALUES(:name, :description, :price)',
            'params' => $queryParams
        ];
        $this->postMethodTemplate($param);
    }

    public function put(array $args): void
    {
        $queryParams = [
            ':id' => 'id',
            ':name' => 'name',
            ':description' => 'description',
            ':price' => 'price'
        ];

        $param = [
            'query' => 'UPDATE product SET name = :name, description = :description, price = :price WHERE id = :id',
            'args' => $args,
            'params' => $queryParams
        ];
        $this->putMethodTemplate($param);
    }

    public function delete(array $args): void
    {
         $params = [
            'query' => 'DELETE FROM product WHERE id = :id',
            'args' => $args
        ];
        $this->deleteMethodTemplate($params);
    }
}
