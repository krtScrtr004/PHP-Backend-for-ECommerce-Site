<?php

interface Validation {
    public static function validate(string $fieldName, mixed $data, int $MIN = 8, int $MAX = 255, callable|array|null $callback = null): array;

    public static function validateFields(array $data): array;

    public static function sanitize(array &$data): void;
};