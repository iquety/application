<?php

declare(strict_types=1);

namespace Iquety\Application;

use InvalidArgumentException;

final class Configuration
{
    /** @var array<string,mixed> */
    private array $parameterList = [];

    public static function loadFrom(string $file): self
    {
        $object = new Configuration();

        $object->load($file);

        return $object;
    }

    public function set(string $name, mixed $value): void
    {
        $name = mb_strtoupper($name);

        $this->parameterList[$name] = $value;
    }

    public function get(string $name, mixed $default = null): mixed
    {
        $name = mb_strtoupper($name);

        return $this->parameterList[$name] ?? $default;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return $this->parameterList;
    }

    public function load(string $file): void
    {
        if (is_file($file) === false) {
            throw new InvalidArgumentException("File $file not found");
        }

        $contents = file_get_contents($file);

        // @codeCoverageIgnoreStart
        if ($contents === false) {
            throw new InvalidArgumentException("Could not read file $file");
        }
        // @codeCoverageIgnoreEnd

        $valueList = parse_ini_string($contents, true, INI_SCANNER_TYPED);

        foreach($valueList as $name => $value) {
            $this->set($name, $value);
        }
    }
}
