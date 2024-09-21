<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine;

use InvalidArgumentException;

class ModuleSet
{
    /** @var array<string,Module> */
    private array $moduleList = [];

    public function add(Module $module): void
    {
        if (isset($this->moduleList[$module::class]) === true) {
            throw new InvalidArgumentException(sprintf(
                'Module %s has already been registered',
                $module::class
            ));
        }

        $this->moduleList[$module::class] = $module;
    }

    public function findByClass(string $moduleClass): ?Module
    {
        return $this->moduleList[$moduleClass] ?? null;
    }

    /** @return array<string,Module> */
    public function toArray(): array
    {
        return $this->moduleList;
    }
}
