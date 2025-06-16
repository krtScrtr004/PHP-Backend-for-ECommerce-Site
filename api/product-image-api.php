<?php

/**
 * Class ProductImageAPI
 *
 * Provides API endpoints for managing product images in the application.
 * Extends ProductAPI and implements CRUD operations for the `product_image` table.
 *
 * Usage:
 * - Use ProductImageAPI::getApi() to get a singleton instance.
 * - Call get(), post(), put(), or delete() to perform respective operations.
 *
 * Methods:
 * - get(array $args = []): Fetches product image records, optionally filtered by arguments.
 * - post(): Creates a new product image record from request data.
 * - put(array $args): Updates an existing product image record with provided data.
 * - delete(array $args): Removes a product image record specified by arguments.
 *
 * This class uses inherited method templates to handle database queries and responses.
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
        $this->getMethodTemplate('product_image', $args);
    }

    public function post(): void
    {
        $this->postMethodTemplate(
            'product_image',
            [
                'id',
                'product_id',
                'image_link'
            ]
        );
    }

    public function put(array $args): void
    {
        $this->putMethodTemplate(
            'product_image',
            $args,
            [
                'image_link'
            ]
        );
    }

    public function delete(array $args): void
    {
        $this->deleteMethodTemplate('product_image', $args);
    }
}
