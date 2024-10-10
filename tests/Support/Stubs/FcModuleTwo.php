<?php

declare(strict_types=1);

namespace Tests\Support\Stubs;

use Iquety\Application\IoEngine\FrontController\CommandSource;
use Iquety\Application\IoEngine\FrontController\CommandSourceSet;
use Iquety\Application\IoEngine\FrontController\FcModule;
use Iquety\Injection\Container;

class FcModuleTwo extends FcModule
{
    /** @param array<string,mixed> $dependencyList */
    public function __construct(
        private string $namespace = '',
        private array $dependencyList = []
    ) {
    }

    public function bootDependencies(Container $container): void
    {
        foreach ($this->dependencyList as $signature => $dependency) {
            $container->addSingleton($signature, $dependency);
        }
    }

    public function bootNamespaces(CommandSourceSet &$sourceSet): void
    {
        $sourceSet->add(new CommandSource($this->namespace));
    }
}
