<?php

/**
 * Class StoreAPI
 *
 * Handles RESTful API operations for the "store" resource.
 * Implements singleton pattern to ensure a single instance and manages validation logic.
 * Extends the base API class to provide CRUD operations for store entities.
 *
 * Methods:
 * - getApi(): Returns the singleton instance of StoreAPI and initializes the validator.
 * - get(array $args = []): Handles GET requests for retrieving store data. Accepts optional filter arguments.
 * - post(): Handles POST requests for creating a new store. Reads input data, generates a slug from the store name, and validates required fields.
 * - put(array $args): Handles PUT requests for updating an existing store. Reads input data, updates the slug, and validates updatable fields.
 * - delete(array $args): Handles DELETE requests for removing a store based on provided arguments.
 *
 * Usage:
 * Use StoreAPI::getApi() to obtain the instance, then call the appropriate method (get, post, put, delete)
 * depending on the HTTP request type and desired operation.
 *
 * Dependencies:
 * - Requires StoreValidation for field validation.
 * - Relies on helper functions such as decodeData() and sentenceToKebabCase().
 */

class StoreAPI extends API
{
    private static $storeAPI;
    protected static $validator;
    protected static $fileName = 'validate-store-fields.json';

    private function __construct() {}

    public static function getApi(): StoreAPI
    {
        if (!isset(self::$storeAPI))
            self::$storeAPI = new self();

        if (!isset(self::$validator))
            self::$validator = StoreValidation::getValidator();

        return self::$storeAPI;
    }

    public function get(array $args = []): void
    {
        $this->getMethodTemplate('store', $args);
    }

    public function post(): void
    {
        $contents = decodeData('php://input');
        $contents['slug'] = strtolower(sentenceToKebabCase($contents['name']))
            ?? throw new ErrorException('Store name is not defined.');

        $this->postMethodTemplate(
            'store',
            [
                'id',
                'type',
                'head_owner_id',
                'name',
                'description',
                'slug',
                'logo_image_link',
                'site_link',
                'email',
                'contact'
            ],
            $contents
        );
    }

    public function put(array $args): void
    {
        $contents = decodeData('php://input');
        $contents['slug'] = strtolower(sentenceToKebabCase($contents['name']))
            ?? throw new ErrorException('Store name is not defined.');

        $this->putMethodTemplate(
            'store',
            $args,
            [
                'type',
                'head_owner_id',
                'name',
                'description',
                'slug',
                'logo_image_link',
                'site_link',
                'email',
                'contact',
                'is_verified'
            ],
            $contents
        );
    }

    public function delete(array $args): void
    {
        $this->deleteMethodTemplate('store', $args);
    }
}
