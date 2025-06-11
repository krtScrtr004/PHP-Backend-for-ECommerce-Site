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
        $mergedArrays = [...$args, ...decodeData('php://input')];
        $queryParams = [
            ':id' => $mergedArrays['id'],
            ':imageLink' => $mergedArrays['imageLink'],
        ];

        $param = [
            'query' => 'UPDATE product_image SET image_link = :imageLink WHERE id = :id',
            'contents' => $mergedArrays,
            'params' => $queryParams
        ];
        $this->putMethodTemplate($param);
    }

    public function delete(array $args): void
    {
        $params = [
            'query' => 'DELETE FROM product_image WHERE id = :id',
            'args' => $args
        ];
        $this->deleteMethodTemplate($params);
    }
}
