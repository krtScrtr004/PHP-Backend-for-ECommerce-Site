<?php

/**
 * Class Id
 *
 * Utility class for generating, converting, and validating UUIDs using the Ramsey\Uuid library.
 * This class provides static methods to work with UUIDs in both string and binary formats.
 * 
 * Methods:
 * - generate(): Generates a new version 4 (random) UUID and returns it as a UuidInterface instance.
 * - toBinary(string|UuidInterface $uuid): Converts a UUID (string or UuidInterface) to its binary representation.
 * - toString(string|UuidInterface $uuid): Converts a UUID (binary string or UuidInterface) to its canonical string representation.
 * - validate(string|UuidInterface $uuid): Validates whether the given value is a valid UUID (accepts both string and UuidInterface).
 *
 * Note: This class cannot be instantiated; all methods are static.
 */

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Id
{
    private function __construct() {}

    public static function generate(): UuidInterface
    {
        return Uuid::uuid4();
    }

    public static function toBinary(string|UuidInterface $uuid): string
    {
        if ($uuid instanceof UuidInterface) {
            return $uuid->getBytes();
        }
        return Uuid::fromString($uuid)->getBytes();
    }

    public static function toString(String|UuidInterface $uuid): string
    {
        if (is_string($uuid)) {
            return Uuid::fromBytes($uuid)->toString();
        }
        return $uuid->toString();
    }

    public static function validate(String|UuidInterface $uuid): bool 
    {
        if (is_string($uuid))
            return Uuid::isValid($uuid);
        return Uuid::isValid($uuid->toString());
    }
}
