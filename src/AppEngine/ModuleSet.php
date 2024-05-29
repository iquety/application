<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

class ModuleSet
{
    /** @var array<string,Bootstrap> */
    private array $moduleList = [];

    public function add(Bootstrap $moduleBootstrap): void
    {
        $this->moduleList[$moduleBootstrap::class] = $moduleBootstrap;
    }

    public function findByClass(string $moduleBootstrapClass): ?Bootstrap
    {
        return $this->moduleList[$moduleBootstrapClass] ?? null;
    }

    /** @return array<string,Bootstrap> */
    public function toArray(): array
    {
        return $this->moduleList;
    }
}
