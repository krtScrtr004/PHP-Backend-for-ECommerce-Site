<?php

/**
 * Product Image API
 *
 * This file provides API endpoints and logic for managing product images.
 * It handles operations such as creating, retrieving, updating,
 * and deleting product images associated with products in the system.
 *
 * Usage:
 * - get(array $args = []): Retrieve product images, optionally filtered by parameters.
 * - post(): Upload a new product image.
 * - put(array $args): Update an existing product image.
 * - delete(array $args): Delete a product image.
 *
 */

class ProductImageAPI extends ProductAPI
{
    private static $productImageAPI;

    public static function getApi(): ProductImageAPI
    {
        if (!isset(self::$productImageAPI))
            self::$productImageAPI = new self();

        self::setValidator();

        return self::$productImageAPI;
    }

    public function get(array $args = []): void
    {
        $params = [
            'query' => 'SELECT * FROM product_image',
            'args' => $args
        ];
        $this->getMethodTemplate($params);
    }

    public function post(): void
    {
        $contents = decodeData('php://input');
        $queryParams = [
            ':productId' => $contents['productId'],
            ':imageLink' => $contents['imageLink'],
        ];

        $param = [
            'query' => 'INSERT INTO product_image(product_id, image_link) VALUES(:productId, :name)',
            'contents' => $contents,
            'params' => $queryParams
        ];
        $this->postMethodTemplate($param);
    }

    public function put(array $args): void
    {
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT')
                throw new LogicException('Bad request.');

            Logger::logAccess('Create PUT request on Product Image API.');

            $contents = decodeData('php://input');
            $mergedArrays = [...$args, ...$contents];

            $validateContents = self::$validator->validateFields($mergedArrays, self::$fileName);
            if (!$validateContents['status'])
                Respond::respondFail($validateContents['message']);

            self::$validator->sanitize($mergedArrays);
            $params = [
                ':id' => $mergedArrays['id'],
                ':imageLink' => $mergedArrays['imageLink'],
            ];

            $stmt = 'UPDATE product_image SET image_link = :imageLink WHERE id = :id';
            $query = $conn->prepare($stmt);
            $query->execute($params);

            Logger::logAccess('Finished PUT request on roduct Image API.');
            Respond::respondSuccess('Product image updated successfully.');
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

            Logger::logAccess('Create DELETE request on Prodiuct Image API.');

            $validateId = self::$validator->validateFields($args, self::$fileName);
            if (!$validateId['status'])
                Respond::respondFail($validateId['message']);

            self::$validator->sanitize($args);
            $params = [':id' => $args['id']];

            $stmt = 'DELETE FROM product_image WHERE id = :id';
            $query = $conn->prepare($stmt);
            $query->execute($params);

            Logger::logAccess('Finished DELETE request on Product Image API.');
            Respond::respondSuccess('Product image deleted successfully.');
        } catch (Exception $e) {
            Respond::respondException($e->getMessage());
        }
    }
}
