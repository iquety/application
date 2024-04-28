<?php

declare(strict_types=1);

namespace Iquety\Application;

class Configuration
{
    private static ?Configuration $instance = null;

    /** @var array<string,mixed> */
    private array $parameterList = [];

    public static function instance(): self
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function set(string $name, mixed $value): void
    {
        $this->parameterList[$name] = $value;
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->parameterList[$name] ?? $default;
    }
}
