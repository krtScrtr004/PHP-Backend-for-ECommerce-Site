<?php

abstract class API
{
    protected static $validator;
    protected static $fileName;


    abstract public function get(array $args = []): void;

    abstract public function post(): void;

    abstract public function put(array $args): void;

    abstract public function delete(array $args): void;

    protected function getMethodTemplate(array $configs)
    {
        $className = get_class($this);

        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET')
                throw new LogicException('Bad Request.');

            $requiredConfigs = ['query', 'args'];
            foreach ($requiredConfigs as $config) {
                if (!isset($configs[$config]))
                    throw new BadMethodCallException("$config is not defined.");
            }

            Logger::logAccess("Create GET request on $className.");

            $params = [];
            $stmt = $configs['query'];

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
        } catch (Exception $e) {
            Respond::respondException($e->getMessage());
        }
    }

    protected function postMethodTemplate(array $configs): void
    {
        $className = get_class($this);

        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST')
                throw new LogicException('Bad request.');

            $requiredConfigs = ['query', 'contents', 'params'];
            foreach ($requiredConfigs as $config) {
                if (!isset($configs[$config]))
                    throw new BadMethodCallException("$config is not defined.");
            }

            Logger::logAccess("Create POST request on $className.");

            $validateContents = static::$validator->validateFields($configs['contents'], static::$fileName);
            if (!$validateContents['status'])
                Respond::respondFail($validateContents['message']);

            static::$validator->sanitize($configs['contents']);

            $query = $conn->prepare($configs['query']);
            $query->execute($configs['params']);

            Logger::logAccess("Finished POST request on $className.");
            Respond::respondSuccess("Post request successful.", code: 201);
        } catch (Exception $e) {
            Respond::respondException($e->getMessage());
        }
    }

    protected function putMethodTemplate(array $configs): void
    {
        $className = get_class($this);
        global $conn;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT')
                throw new LogicException('Bad request.');

            Logger::logAccess("Create PUT request on $className");

            $requiredConfigs = ['query', 'contents', 'params'];
            foreach ($requiredConfigs as $config) {
                if (!isset($configs[$config]))
                    throw new BadMethodCallException("$config is not defined.");
            }

            $validateContents = static::$validator->validateFields($configs['contents'], static::$fileName);
            if (!$validateContents['status'])
                Respond::respondFail($validateContents['message']);

            static::$validator->sanitize($configs['contents']);
            $query = $conn->prepare($configs['query']);
            $query->execute($configs['params']);

            Logger::logAccess("Finished PUT request on $className");
            Respond::respondSuccess('Put request successful.');
        } catch (Exception $e) {
            Respond::respondException($e->getMessage());
        }
    }
}
