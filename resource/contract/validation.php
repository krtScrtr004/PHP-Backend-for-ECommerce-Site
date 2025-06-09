<?php

abstract class Validation
{
    abstract public static function getValidator();

    public function validate(string $fieldName, mixed $data, int $MIN = 8, int $MAX = 255, callable|array|null $callback = null): array
    {
        $fieldName = ucwords($fieldName);

        $validationResult = [
            'status' => true,
            'message' => "$fieldName is valid."
        ];

        // Undefined / Null validation
        if (!$data) {
            $validationResult['status'] = false;
            $validationResult['message'] = "$fieldName is not defined";
        }
        // Empty string validation
        else if (empty($data)) {
            $validationResult['status'] = false;
            $validationResult['message'] = "$fieldName cannot be empty.";
        } else if (strcasecmp($fieldName, 'id') !== 0) {
            // Length requirement validation
            if (strlen($data) < $MIN || strlen($data) > $MAX) {
                $validationResult['status'] = false;
                $validationResult['message'] = "$fieldName must be between $MIN and $MAX only.";
            }
        }
        // Callback function is defined
        else if ($callback) {
            $callbackReturn = null;
            if (is_callable($callback))
                $callbackReturn = call_user_func($callback, $data);
            else if (is_array($callback)) 
                $callbackReturn = call_user_func([$callback[0], $callback[1]], $data);

            if (!$callbackReturn['status'])
                $validationResult = $callbackReturn;
        }

        return $validationResult;
    }

    public function validateFields(array $data, String $fileName): array
    {
        // Get all fields to validate
        $presentFields = array_keys($data);

        $dataPath = DATA_PATH . $fileName;
        if (!file_exists($dataPath)) {
            throw new ErrorException("$dataPath does not exists.");
        }

        $validateFields = decodeData($dataPath);
        foreach ($validateFields as $field) {
            // Skip field validation for fields that are not present
            if (!in_array($field['name'], $presentFields))
                continue;

            $params = [
                'fieldName' => $field['name'],
                'data' => $data[$field['data']],
            ];
            if ($field['min'])
                $params['MIN'] = $field['min'];
            if ($field['max'])
                $params['MAX'] = $field['max'];
            if ($field['callback'])
                $params['callback'] = [$this, $field['callback']];

            $validationResult = $this->validate(...$params);
            if (!$validationResult['status'])
                return $validationResult;
        }
        return ['status' => true];
    }

    abstract public static function sanitize(array &$data): void;
};
