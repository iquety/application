<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Console;

use InvalidArgumentException;

class RoutineSourceSet
{
    /** @var array<string,RoutineSource> */
    private array $sourceList = [];

    public function __construct(private string $moduleClass)
    {
    }

    public function add(RoutineSource $source): void
    {
        $index = $source->getIdentity();

        if (isset($this->sourceList[$index]) === true) {
            throw new InvalidArgumentException(
                'The specified source already exists'
            );
        }

        $this->sourceList[$index] = $source;
    }

    public function hasSources(): bool
    {
        return $this->sourceList !== [];
    }

    public function getModuleClass(): string
    {
        return $this->moduleClass;
    }

    /** @return array<string,RoutineSource> */
    public function toArray(): array
    {
        return $this->sourceList;
    }
}
