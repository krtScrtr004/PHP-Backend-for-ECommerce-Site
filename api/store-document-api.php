<?php

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
