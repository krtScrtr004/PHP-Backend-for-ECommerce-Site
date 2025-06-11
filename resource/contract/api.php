<?php

abstract class API
{
    protected static $validator;
    protected static $fileName;


    abstract public function get(array $args = []): void;

    abstract public function post(): void;

    abstract public function put(array $args): void;

    abstract public function delete(array $args): void;

    protected function getFunctionTemplate(array $configs)
    {
        $className = get_class($this);

        global $conn;
        try {
             if ($_SERVER['REQUEST_METHOD'] !== 'GET')
                throw new LogicException('Bad Request.');

            $requiredConfigs = ['query', 'args'];
            foreach ($requiredConfigs as $config){
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
}
