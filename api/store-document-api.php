<?php

/**
 * Class StoreDocumentAPI
 *
 * Handles RESTful API operations for the `store_document` resource.
 * Implements singleton pattern to ensure a single instance and manages validation setup.
 *
 * Methods:
 * - getApi(): Returns the singleton instance of StoreDocumentAPI and initializes the validator.
 * - get(array $args = []): Handles GET requests for retrieving store document records. Accepts optional filter arguments.
 * - post(): Handles POST requests for creating a new store document. Expects required fields: 'store_id', 'tin', 'vat_status', 'gov_id_image_link', and 'business_permit_image_link'.
 * - put(array $args): Handles PUT requests for updating an existing store document. Requires the record identifier in $args and updatable fields: 'tin', 'vat_status', 'gov_id_image_link', and 'business_permit_image_link'.
 * - delete(array $args): Handles DELETE requests for removing a store document record. Requires the record identifier in $args.
 *
 * Utilizes method templates for standard CRUD operations and integrates field validation using an external validator.
 */

class StoreDocumentAPI extends API
{
    private static $storeDocumentAPI;
    protected static $validator;
    protected static $fileName = 'validate-store-fields.json';

    private function __construct() {}

    public static function getApi(): StoreDocumentAPI
    {
        if (!isset(self::$storeDocumentAPI))
            self::$storeDocumentAPI = new self();

        if (!isset(self::$validator))
            self::$validator = StoreValidation::getValidator();

        return self::$storeDocumentAPI;
    }

    public function get(array $args = []): void
    {
        $this->getMethodTemplate('store_document', $args);
    }

    public function post(): void
    {
        $this->postMethodTemplate(
            'store_document',
            [
                'store_id',
                'tin',
                'vat_status',
                'gov_id_image_link',
                'business_permit_image_link'
            ]
        );
    }

    public function put(array $args): void
    {
        $this->putMethodTemplate(
            'store_document',
            $args,
            [
                'tin',
                'vat_status',
                'gov_id_image_link',
                'business_permit_image_link'
            ]
        );
    }

    public function delete(array $args): void
    {
        $this->deleteMethodTemplate('store_document', $args);
    }
}
