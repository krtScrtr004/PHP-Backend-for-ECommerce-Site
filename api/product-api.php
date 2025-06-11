<?php

/**
 * Product API
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
        $this->getFunctionTemplate($params);
    }

    public function post(): void
    {
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST')
                throw new LogicException('Bad request.');

            Logger::logAccess('Create POST request on Product API.');

            $contents = decodeData('php://input');

            $validateContents = self::$validator->validateFields($contents, self::$fileName);
            if (!$validateContents['status'])
                Respond::respondFail($validateContents['message']);

            self::$validator->sanitize($contents);
            $params = [
                ':name' => $contents['name'],
                ':description' => $contents['description'],
                ':price' => $contents['price']
            ];

            $stmt = 'INSERT INTO product(name, description, price) VALUES(:name, :description, :price)';
            $query = $conn->prepare($stmt);
            $query->execute($params);

            Logger::logAccess('Finished POST request on Product API.');
            Respond::respondSuccess('Product created successfully.', code: 201);
        } catch (Exception $e) {
            Respond::respondException($e->getMessage());
        }
    }

    public function put(array $args): void
    {
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT')
                throw new LogicException('Bad request.');

            Logger::logAccess('Create PUT request on Product API.');

            $contents = decodeData('php://input');
            $mergedArrays = [...$args, ...$contents];

            $validateContents = self::$validator->validateFields($mergedArrays, self::$fileName);
            if (!$validateContents['status'])
                Respond::respondFail($validateContents['message']);

            self::$validator->sanitize($mergedArrays);
            $params = [
                ':id' => $mergedArrays['id'],
                ':name' => $mergedArrays['name'],
                ':description' => $mergedArrays['description'],
                ':price' => $mergedArrays['price']
            ];

            $stmt = 'UPDATE product SET name = :name, description = :description, price = :price WHERE id = :id';
            $query = $conn->prepare($stmt);
            $query->execute($params);

            Logger::logAccess('Finished PUT request on roduct API.');
            Respond::respondSuccess('Product updated successfully.');
        } catch (Exception $e) {
            Respond::respondException($e->getMessage());
        }
    }

    public function delete(array $args): void
    {
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE')
                throw new LogicException('Bad request.');

            Logger::logAccess('Create DELETE request on Prodiuct API.');

            $validateId = self::$validator->validateFields($args, self::$fileName);
            if (!$validateId['status'])
                Respond::respondFail($validateId['message']);

            self::$validator->sanitize($args);
            $params = [':id' => $args['id']];

            $stmt = 'DELETE FROM product WHERE id = :id';
            $query = $conn->prepare($stmt);
            $query->execute($params);

            Logger::logAccess('Finished DELETE request on Product API.');
            Respond::respondSuccess('Product deleted successfully.');
        } catch (Exception $e) {
            Respond::respondException($e->getMessage());
        }
    }
}
