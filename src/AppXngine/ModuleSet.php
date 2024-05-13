<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

use Iquety\Application\Application;
use Iquety\Application\Bootstrap;

class ModuleSet
{
    private array $moduleList = [];

    public function add(Bootstrap $moduleBootstrap): void
    {
        $this->moduleList[$moduleBootstrap::class] = $moduleBootstrap;
    }

    public function findByClass(string $moduleBootstrapClass): ?Bootstrap
    {
        return $this->moduleList[$moduleBootstrapClass] ?? null;
    }

    public function toArray(): array
    {
        return $this->moduleList;
    }
}