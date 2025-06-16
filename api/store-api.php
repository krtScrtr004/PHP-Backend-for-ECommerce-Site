<?php

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
