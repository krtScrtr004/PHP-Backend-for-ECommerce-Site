<?php

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
}
