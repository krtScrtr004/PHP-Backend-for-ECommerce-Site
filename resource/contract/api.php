<?php

/**
 * Abstract class API
 *
 * This abstract class serves as a foundational template for building RESTful API endpoints in PHP.
 * It defines the structure and reusable method templates for handling HTTP request methods (GET, POST, PUT, DELETE)
 * with built-in validation, logging, and standardized response handling.
 *
 * Usage:
 * - Extend this class to implement specific API resource handlers.
 * - Implement the abstract methods: get(), post(), put(), and delete() to define resource-specific logic.
 * - Use the provided protected method templates (getMethodTemplate, postMethodTemplate, putMethodTemplate, deleteMethodTemplate)
 *   to automate common CRUD operations with validation and consistent response formatting.
 *
 * Properties:
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
 * - getMethodTemplate(string $table, array $args): Automates GET request handling, including query preparation,
 *   parameter binding, validation, and response formatting.
 * - postMethodTemplate(string $table, array $columns, array $contents = []): Automates POST request handling,
 *   including input validation, sanitization, query execution, and response formatting.
 * - putMethodTemplate(string $table, array $args, array $columns, array $contents = []): Automates PUT request handling,
 *   including input validation, sanitization, query execution, and response formatting.
 * - deleteMethodTemplate(string $table, array $args): Automates DELETE request handling,
 *   including input validation, sanitization, query execution, and response formatting.
 *
 * Each method template expects specific parameters and throws exceptions or returns standardized responses
 * in case of errors or invalid requests.
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
     * - @param table -- table name
     * - @param args  -- query parameter (eg. ID)
     * 
     */
    protected function getMethodTemplate(String $table, array $args): void
    {
        $className = get_class($this);
        global $conn;

        if ($_SERVER['REQUEST_METHOD'] !== 'GET')
            throw new LogicException('Bad Request.');

        Logger::logAccess("Create GET request on $className.");

        $params = [];

        $stmt = "SELECT * FROM `$table`";

        if (count($args) > 0) {
            $stmt .= ' WHERE ';

            $validateContents = static::$validator->validateFields($args, static::$fileName);
            if ($validateContents['status']) {
                // Collect conditions in an array
                $conditions = [];
                foreach ($args as $key => $value) {
                    $conditionKey = strtolower(camelToSnakeCase($key));
                    $conditions[] = "$conditionKey = :$key";
                    $params["$key"] = $value;
                }
                // Join conditions with AND in the WHERE clause
                $stmt .= implode(' AND ', $conditions);
            } else {
                Respond::respondFail($validateContents['message']);
            }
        }
        static::$validator->sanitizeData($params);

        $query = $conn->prepare($stmt);
        $query->execute($params);
        $result = $query->fetchAll();
        foreach ($result as &$data) { // Stringify id info
            foreach ($data as $column => $row) {
                if (preg_match('/(^id$|Id$|_id$)/', $column)) 
                    $data[$column] = Id::toString($row);
            }
        }

        Logger::logAccess("Finished GET request on $className.");
        Respond::respondSuccess(data: $result);
    }

    /**
     * 
     * This method is used to automate the creation of POST method
     * 
     * - @param table    -- table name
     * - @param columns  -- column names where data is inserted
     * - @param contets -- passed data on request (optional)
     * 
     */
    protected function postMethodTemplate(String $table, array $columns, array $contents = []): void
    {
        $className = get_class($this);
        global $conn;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            throw new LogicException('Bad request.');

        Logger::logAccess("Create POST request on $className.");

        if (empty($contents))
            $contents = decodeData('php://input');

        $validateContents = static::$validator->validateFields($contents, static::$fileName);
        if (!$validateContents['status'])
            Respond::respondFail($validateContents['message']);

        // Build the binding paramters for query
        $params = [];
        foreach ($columns as $value) {
            $valueSnakeCase = snakeToCamelCase($value);
            if (preg_match('/(^password$|Password$)/', $value))
                $params[$valueSnakeCase] = password_hash($contents[camelToSnakeCase($value)], PASSWORD_ARGON2ID);
            else if (preg_match('/^id$/', $value))
                $params[$valueSnakeCase] = Id::generate();
            else
                $params[$valueSnakeCase] = $contents[$value];
        }

        static::$validator->sanitizeData($params);

        // Building query statement
        $columnList = implode(',', $columns);
        $values = implode(',', array_map(fn($v) => ':' . snakeToCamelCase($v), $columns));

        $stmt = "INSERT INTO `$table`({$columnList}) VALUES({$values})";

        $query = $conn->prepare($stmt);
        $query->execute($params);

        Logger::logAccess("Finished POST request on $className.");
        Respond::respondSuccess("Post request successful.", code: 201);
    }

    /**
     * 
     * This method is used to automate the creation of PUT method
     * 
     * - @param table   -- table name
     * - @param args    -- additional data to the form content (eg. id)
     * - @param columns -- column names where data is updated
     * - @param contets -- passed data on request (optional)
     * 
     */
    protected function putMethodTemplate(String $table, array $args, array $columns, array $contents = []): void
    {
        $className = get_class($this);
        global $conn;

        if ($_SERVER['REQUEST_METHOD'] !== 'PUT')
            throw new LogicException('Bad request.');

        Logger::logAccess("Create PUT request on $className");

        if (empty($contents))
            $contents = [...$args, ...decodeData('php://input')];
        else
            $contents = [...$args, ...$contents];

        $validateContents = static::$validator->validateFields($contents, static::$fileName);
        if (!$validateContents['status'])
            Respond::respondFail($validateContents['message']);

        // Build the binding paramters for query
        $params = [];
        foreach ($columns as $value) {
            $valueSnakeCase = snakeToCamelCase($value);
            if (preg_match('/(^password$|Password$)/', $value))
                $params[$valueSnakeCase] = password_hash($contents[camelToSnakeCase($value)], PASSWORD_ARGON2ID);
            else
                $params[$valueSnakeCase] = $contents[$value];
        }
        
        $idName = '';
        foreach ($args as $key => $value) {
            if (preg_match('/^id$|Id$|_id$/', $key)){
                $params[$key] = $value;
                $idName = $key;
            }
        }

        $updateStmt = implode(', ', array_map(function ($val) {
            return $val . ' = :' . snakeToCamelCase($val);
        }, $columns));
        $stmt = "UPDATE `$table` SET $updateStmt WHERE " . strtolower(camelToSnakeCase($idName)) . " = :$idName";

        static::$validator->sanitizeData($params);
        $query = $conn->prepare($stmt);
        $query->execute($params);

        Logger::logAccess("Finished PUT request on $className");
        Respond::respondSuccess('Put request successful.');
    }

    /**
     * 
     * This method is used to automate the creation of DELETE method
     * 
     * - @param table -- table name
     * - @param args  -- query parameter (eg. ID)
     * 
     */
    protected function deleteMethodTemplate(String $table, array $args): void
    {
        $className = get_class($this);
        global $conn;

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE')
            throw new LogicException('Bad request.');

        Logger::logAccess("Create DELETE request on $className.");

        $validateId = static::$validator->validateFields($args, static::$fileName);
        if (!$validateId['status'])
            Respond::respondFail($validateId['message']);

        $idName = '';
        foreach ($args as $key => $value) {
            if (preg_match('/^id$|Id$|_id$/', $key)){
                $idName = $key;
            }
        }

        $params = ["$idName" => $args[$idName] ?? throw new BadMethodCallException('Id is not defined.')];

        static::$validator->sanitizeData($params);
        $stmt = "DELETE FROM `$table` WHERE " . strtolower(camelToSnakeCase($idName)) . " = :" . $idName;

        $query = $conn->prepare($stmt);
        $query->execute($params);

        Logger::logAccess("Finished DELETE request on $className.");
        Respond::respondSuccess('Delete request successful.');
    }
}
