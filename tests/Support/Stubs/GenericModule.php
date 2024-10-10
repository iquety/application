<?php

declare(strict_types=1);

namespace Tests\Support\Stubs;

use Iquety\Application\IoEngine\Module;
use Iquety\Injection\Container;

class GenericModule implements Module
{
    /** @param array<string,mixed> $dependencyList */
    public function __construct(private array $dependencyList = [])
    {
    }

    public function bootDependencies(Container $container): void
    {
        foreach ($this->dependencyList as $signature => $dependency) {
            $container->addFactory($signature, $dependency);
        }
    }

    public function getActionType(): string
    {
        return 'generic';
    }

    public function getErrorActionClass(): string
    {
        return 'generic';
    }

    public function getMainActionClass(): string
    {
        return 'generic';
    }

    public function getNotFoundActionClass(): string
    {
        return 'generic';
    }
}
