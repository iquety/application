<?php

declare(strict_types=1);

namespace Freep\Application\Http;

interface Session
{
    public function add(string $name, $value): void;

    public function param(): string;

    public function remove(string $name): void;

    public function all(): array;
}
