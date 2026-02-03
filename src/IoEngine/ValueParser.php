<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine;

class ValueParser
{
    /** @param array<mixed>|float|int|string $value */
    public function __construct(private mixed $value)
    {
    }

    /** @return array<mixed>|float|int|string */
    public function withCorrectType(): array|bool|float|int|null|object|string
    {
        return $this->typed($this->value);
    }

    /**
     * @param array<mixed>|float|int|string $value
     * @return array<mixed>|float|int|string
     */
    public function typed(mixed $value): array|bool|float|int|null|object|string
    {
        if ($value === 'null') {
            return null;
        }

        if ($value === 'false') {
            return false;
        }

        if ($value === 'true') {
            return true;
        }

        if (is_array($value) === true) {
            foreach ($value as $key => $subValue) {
                $value[$key] = $this->typed($subValue);
            }

            return $value;
        }

        if (is_numeric($value) === false) {
            return $value;
        }

        if (is_int($value + 0) === true) {
            return (int)$value;
        }

        return (float)$value;
    }
}
