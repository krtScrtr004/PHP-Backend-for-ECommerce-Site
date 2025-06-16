<?php

class StoreStaffAPI extends API
{
    private static $storeStaffAPI;
    protected static $validator;
    protected static $fileName = 'validate-staff-fields.json';

    private function __construct() {}

    public static function getApi(): StoreStaffAPI
    {
        if (!isset(self::$storeStaffAPI))
            self::$storeStaffAPI = new self();

        if (!isset(self::$validator))
            self::$validator = StaffValidation::getValidator();

        return self::$storeStaffAPI;
    }

    public function get(array $args = []): void
    {
        $this->getMethodTemplate('store_staff', $args);
    }

    public function post(): void
    {
        $this->postMethodTemplate('store_staff',['id', 'store_id', 'user_id']);
    }

    public function put(array $args): void
    {
        $this->putMethodTemplate('store_staff',$args,['store_id', 'user_id']);
    }

    public function delete(array $args): void
    {
        $this->deleteMethodTemplate('store_staff', $args);
    }
}
