<?php

/**
 * Class ProductImageAPI
 *
 * Handles API operations related to product images in the application.
 * Inherits from ProductAPI and provides CRUD (Create, Read, Update, Delete) methods
 * for managing records in the `product_image` database table.
 *
 * Usage:
 * - Use ProductImageAPI::getApi() to obtain a singleton instance of this API handler.
 * - Call the respective methods (get, post, put, delete) to perform operations on product images.
 *
 * Methods:
 * - get(array $args = []): Retrieves product image records from the database. Accepts optional filter arguments.
 * - post(): Creates a new product image record using data from the request body.
 * - put(array $args): Updates an existing product image record. Merges URL arguments and request body data.
 * - delete(array $args): Deletes a product image record specified by the provided arguments.
 *
 * This class uses method templates (getMethodTemplate, postMethodTemplate, etc.) inherited from ProductAPI
 * to standardize the execution of database queries and response formatting.
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
        $this->getMethodTemplate([
            'table' => 'product_image',
            'args' => $args
        ]);
    }

    public function post(): void
    {
        $this->postMethodTemplate([
            'table' => 'product_image',
            'columns' => [
                'product_id',
                'image_link'
            ]
        ]);
    }

    public function put(array $args): void
    {
        $this->putMethodTemplate([
            'table' => 'product_image',
            'args' => $args,
            'columns' => [
                'product_id',
                'image_link'
            ]
        ]);
    }

    public function delete(array $args): void
    {
        $this->deleteMethodTemplate([
            'table' => 'product_image',
            'args' => $args
        ]);
    }
}
