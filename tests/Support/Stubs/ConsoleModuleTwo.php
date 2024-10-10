<?php

declare(strict_types=1);

namespace Tests\Support\Stubs;

use Iquety\Application\IoEngine\Console\ConsoleModule;
use Iquety\Application\IoEngine\Console\RoutineSource;
use Iquety\Application\IoEngine\Console\RoutineSourceSet;
use Iquety\Injection\Container;

class ConsoleModuleTwo extends ConsoleModule
{
    /** @param array<string,mixed> $dependencyList */
    public function __construct(
        private string $directory = '',
        private array $dependencyList = [],
        private string $rootPath = ''
    ) {
        if ($rootPath === '') {
            $this->rootPath = __DIR__;
        }
    }

    public function bootDependencies(Container $container): void
    {
        foreach ($this->dependencyList as $signature => $dependency) {
            $container->addSingleton($signature, $dependency);
        }
    }

    public function bootRoutineDirectories(RoutineSourceSet &$sourceSet): void
    {
        $sourceSet->add(new RoutineSource($this->directory));
    }

    public function getCommandName(): string
    {
        return 'test-script';
    }

    /** Devolve o diretório real da aplicação que implementa o Console */
    public function getCommandPath(): string
    {
        return $this->rootPath;
    }
}
