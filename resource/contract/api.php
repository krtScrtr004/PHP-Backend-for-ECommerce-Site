<?php

interface API {
    public function get(array $args = []): void;

    public function post(): void;

    public function put(array $args): void;

    public function delete(array $args): void;
}