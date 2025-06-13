<?php

/**
 * Abstract class API
 *
 * This abstract class provides a standardized template for implementing RESTful API endpoints
 * in PHP. It defines the structure and reusable method templates for handling HTTP request
 * methods (GET, POST, PUT, DELETE) with built-in validation, logging, and response handling.
 *
 * Usage:
 * - Extend this class to create specific API resource handlers.
 * - Implement the abstract methods: get(), post(), put(), and delete() to define resource-specific logic.
 * - Use the provided protected method templates (getMethodTemplate, postMethodTemplate, putMethodTemplate, deleteMethodTemplate)
 *   to automate common CRUD operations with validation and consistent response formatting.
 *
 * Properties:
 * - $className: Stores the name of the child class for logging and identification.
 * - static $validator: Reference to a validator instance for input validation and sanitization.
 * - static $fileName: Reference to a file or schema name used during validation.
 *
 * Abstract Methods:
 * - get(array $args = []): Handle GET requests for retrieving resources.
 * - post(): Handle POST requests for creating new resources.
 * - put(array $args): Handle PUT requests for updating existing resources.
 * - delete(array $args): Handle DELETE requests for removing resources.
 *
 * Method Templates:
 * - getMethodTemplate(array $configs): Automates GET request handling, including query preparation,
 *   parameter binding, validation, and response formatting.
 * - postMethodTemplate(array $configs): Automates POST request handling, including input validation,
 *   sanitization, query execution, and response formatting.
 * - putMethodTemplate(array $configs): Automates PUT request handling, including input validation,
 *   sanitization, query execution, and response formatting.
 * - deleteMethodTemplate(array $configs): Automates DELETE request handling, including input validation,
 *   sanitization, query execution, and response formatting.
 *
 * Each method template expects a configuration array with required keys (such as 'query', 'args', 'contents', 'params')
 * and throws exceptions or returns standardized responses in case of errors or invalid requests.
 *
 * This class is intended to be used as a base for building robust, maintainable, and secure RESTful APIs.
 */

abstract class API
{
    protected static $validator;
    protected static $fileName;


    /**
     * Abstract Methods to Implement
     */

    abstract public function get(array $args = []): void;

    abstract public function post(): void;

    abstract public function put(array $args): void;

    abstract public function delete(array $args): void;


    /**
     * Method Templates for HTTP Request Methods
     */

    /**
     * 
     * This method is used to automate the creation of GET method
     * 
     * Required keys for @param configs
     * - @param query       -- SELECT query array. It must contain the ff:
     *      > @param table  -- table name (REQUIRED)
     * - @param args        -- query parameter (eg. ID)
     * 
     */
    protected function getMethodTemplate(array $configs): void
    {
        $className = get_class($this);
        global $conn;

        if ($_SERVER['REQUEST_METHOD'] !== 'GET')
            throw new LogicException('Bad Request.');

        $requiredConfigs = ['table', 'args'];
        foreach ($requiredConfigs as $config) {
            if (!isset($configs[$config]))
                throw new BadMethodCallException("$config is not defined.");
        }

        Logger::logAccess("Create GET request on $className.");

        $params = [];

        $stmt = "SELECT * FROM {$configs['table']}";

        if (count($configs['args']) > 0) {
            $stmt .= ' WHERE ';

            $validateContents = static::$validator->validateFields($configs['args'], static::$fileName);
            if ($validateContents['status']) {
                // Collect conditions in an array
                $conditions = [];
                foreach ($configs['args'] as $key => $value) {
                    $conditionKey = strtolower(camelToSnakeCase($key));
                    $conditions[] = "$conditionKey = :$key";
                    $params[":$key"] = $value;
                }
                // Join conditions with AND in the WHERE clause
                $stmt .= implode(' AND ', $conditions);
            } else {
                Respond::respondFail($validateContents['message']);
            }
        }

        $query = $conn->prepare($stmt);
        $query->execute($params);
        $result = $query->fetchAll();

        Logger::logAccess("Finished GET request on $className.");
        Respond::respondSuccess(data: $result);
    }

    /**
     * 
     * This method is used to automate the creation of POST method
     * 
     * Required keys for @param configs
     * - @param query    -- INSERT query to execute
     * - @param params   -- parameter array for binding values to query statement
     * 
     */
    protected function postMethodTemplate(array $configs): void
    {
        $className = get_class($this);
        global $conn;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            throw new LogicException('Bad request.');

        $requiredConfigs = ['table', 'columns'];
        foreach ($requiredConfigs as $config) {
            if (!isset($configs[$config]))
                throw new BadMethodCallException("$config is not defined.");
        }

        Logger::logAccess("Create POST request on $className.");

        $contents = $configs['contents'] ?? decodeData('php://input');
        $validateContents = static::$validator->validateFields($contents, static::$fileName);
        if (!$validateContents['status'])
            Respond::respondFail($validateContents['message']);

        $params = [];
        foreach ($configs['columns'] as $value) {
            $value = snakeToCamelCase($value);
            if (preg_match('/(^password$|Password$)/', $value))
                $params[$value] = password_hash($contents[camelToSnakeCase($value)], PASSWORD_ARGON2ID);
            else
                $params[$value] = $contents[$value];
        }

        static::$validator->sanitize($contents);

        // Building query statement
        $columns = implode(',', $configs['columns']);
        $values = implode(',', array_map(fn($v) => ':' . snakeToCamelCase($v), $configs['columns']));

        $stmt = "INSERT INTO {$configs['table']}({$columns}) VALUES({$values})";

        $query = $conn->prepare($stmt);
        $query->execute($params);

        Logger::logAccess("Finished POST request on $className.");
        Respond::respondSuccess("Post request successful.", code: 201);
    }

    /**
     * 
     * This method is used to automate the creation of PUT method
     * 
     * Required keys for @param configs
     * - @param query    -- UPDATE query to execute
     * - @param args     -- additional data to the form content (eg. id)
     * - @param params   -- parameter array for binding values to query statement
     * 
     */
    protected function putMethodTemplate(array $configs): void
    {
        $className = get_class($this);
        global $conn;

        if ($_SERVER['REQUEST_METHOD'] !== 'PUT')
            throw new LogicException('Bad request.');

        Logger::logAccess("Create PUT request on $className");

        $requiredConfigs = ['query', 'args', 'params'];
        foreach ($requiredConfigs as $config) {
            if (!isset($configs[$config]))
                throw new BadMethodCallException("$config is not defined.");
        }

        $contents = [...$configs['args'], ...$configs['content'] ?? decodeData('php://input')];

        $validateContents = static::$validator->validateFields($contents, static::$fileName);
        if (!$validateContents['status'])
            Respond::respondFail($validateContents['message']);

        $params = [];
        foreach ($configs['params'] as $key => $value) {
            if (preg_match('/(^:password$|Password$)/', $key))
                $params[$key] = password_hash($contents[$value], PASSWORD_ARGON2ID);
            else
                $params[$key] = $contents[$value];
        }

        static::$validator->sanitize($contents);
        $query = $conn->prepare($configs['query']);
        $query->execute($params);

        Logger::logAccess("Finished PUT request on $className");
        Respond::respondSuccess('Put request successful.');
    }

    /**
     * 
     * This method is used to automate the creation of DELETE method
     * 
     * Required keys for @param configs
     * - @param query -- DELETE query to execute
     * - @param args  -- query parameter (eg. ID)
     * 
     */
    protected function deleteMethodTemplate(array $configs): void
    {
        $className = get_class($this);
        global $conn;

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE')
            throw new LogicException('Bad request.');

        $requiredConfigs = ['query', 'args'];
        foreach ($requiredConfigs as $config) {
            if (!isset($configs[$config]))
                throw new BadMethodCallException("$config is not defined.");
        }
        $args = $configs['args'];

        Logger::logAccess("Create DELETE request on $className.");

        $validateId = static::$validator->validateFields($args, static::$fileName);
        if (!$validateId['status'])
            Respond::respondFail($validateId['message']);

        static::$validator->sanitize($args);
        $params = [':id' => $args['id']];

        $query = $conn->prepare($configs['query']);
        $query->execute($params);

        Logger::logAccess("Finished DELETE request on $className.");
        Respond::respondSuccess('Delete request successful.');
    }
}
