<?php

/**
 * 
 * Product API class
 *
 * This file provides API endpoints and logic for managing products.
 * It handles operations such as creating, retrieving, updating,
 * and deleting products in the system.
 *
 * Usage:
 * - get(array $args = []): Retrieve products, optionally filtered by parameters.
 * - post(): Create a new product.
 * - put(array $args): Update an existing product.
 * - delete(array $args): Delete a product.
 *
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
        $contents = decodeData('php://input');
        $queryParams = [
            ':name' => $contents['name'],
            ':description' => $contents['description'],
            ':price' => $contents['price']
        ];

        $param = [
            'query' => 'INSERT INTO product(name, description, price) VALUES(:name, :description, :price)',
            'contents' => $contents,
            'params' => $queryParams
        ];
        $this->postMethodTemplate($param);
    }

    public function put(array $args): void
    {
        $mergedArrays = [...$args, ...decodeData('php://input')];
        $queryParams = [
            ':id' => $mergedArrays['id'],
            ':name' => $mergedArrays['name'],
            ':description' => $mergedArrays['description'],
            ':price' => $mergedArrays['price']
        ];

        $param = [
            'query' => 'UPDATE product SET name = :name, description = :description, price = :price WHERE id = :id',
            'contents' => $mergedArrays,
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
