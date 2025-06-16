<?php

/**
 * Class StoreStaffAPI
 *
 * Handles RESTful API operations for the 'store_staff' resource.
 * Implements singleton pattern to ensure a single instance and manages validation logic.
 *
 * Methods:
 * - getApi(): Returns the singleton instance of StoreStaffAPI and initializes the validator.
 * - get(array $args = []): Handles GET requests for retrieving store staff data. Accepts optional filter arguments.
 * - post(): Handles POST requests for creating a new store staff entry. Expects 'id', 'store_id', and 'user_id' fields.
 * - put(array $args): Handles PUT requests for updating an existing store staff entry. Requires 'store_id' and 'user_id' fields, and accepts additional arguments for filtering.
 * - delete(array $args): Handles DELETE requests for removing store staff entries. Accepts arguments to specify which entries to delete.
 *
 * Utilizes method templates (getMethodTemplate, postMethodTemplate, putMethodTemplate, deleteMethodTemplate)
 * to standardize CRUD operations for the 'store_staff' table.
 */

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
