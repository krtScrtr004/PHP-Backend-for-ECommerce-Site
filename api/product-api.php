<?php
/**
 * Class ProductAPI
 *
 * Provides a RESTful API for managing products in a database.
 * Inherits from the base API class and implements CRUD operations
 * (Create, Read, Update, Delete) for the 'product' resource.
 *
 * Key Features:
 * - Singleton pattern: Ensures only one instance of ProductAPI exists.
 * - Handles HTTP GET, POST, PUT, and DELETE requests for products.
 * - Validates product data using a JSON schema validator.
 * - Interacts with the database for all product operations.
 *
 * Main Methods:
 * - getApi(): Returns the singleton instance and sets up the validator.
 * - get(array $args = []): Retrieves products, with optional filtering.
 * - post(): Creates a new product after validating input data.
 * - put(array $args): Updates an existing product with merged input.
 * - delete(array $args): Deletes a product by its ID.
 *
 * Usage:
 * Use ProductAPI::getApi() to obtain the instance, then call the public methods
 * to handle HTTP requests for product resources.
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
        $this->getMethodTemplate('product', $args);
    }

    public function post(): void
    {
        $columns = ['id', 'store_id', 'name', 'description', 'price'];
        $contents = decodeData('php://input');
        if (isset($contents['currency']))
            array_push($columns, 'currency');

        $this->postMethodTemplate('product',$columns,$contents);
    }

    public function put(array $args): void
    {
        $this->putMethodTemplate(
            'product',
            $args,
            [
                'name',
                'description',
                'price',
                'currency'
            ]
        );
    }

    public function delete(array $args): void
    {
        $this->deleteMethodTemplate('product',$args);
    }
}
