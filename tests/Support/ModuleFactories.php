<?php

declare(strict_types=1);

namespace Tests\Support;

use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpMethod;
use Iquety\Application\Http\Session;
use Iquety\Application\IoEngine\Console\ConsoleModule;
use Iquety\Application\IoEngine\FrontController\FcModule;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\Mvc\MvcModule;
use Tests\Support\Stubs\ConsoleModuleOne;
use Tests\Support\Stubs\ConsoleModuleTwo;
use Tests\Support\Stubs\FcModuleOne;
use Tests\Support\Stubs\FcModuleTwo;
use Tests\Support\Stubs\GenericModule;
use Tests\Support\Stubs\MvcModuleOne;
use Tests\Support\Stubs\MvcModuleTwo;

trait ModuleFactories
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param array<string,mixed> $dependencyList
     */
    protected function makeGenericModule(array $dependencyList = []): Module
    {
        return new GenericModule($dependencyList);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param array<string,mixed> $dependencyList
     */
    protected function makeConsoleModuleOne(
        string $commandDirectory = '',
        array $dependencyList = [],
        string $commandRootPath = '',
    ): ConsoleModule {

        return new ConsoleModuleOne($commandDirectory, $dependencyList, $commandRootPath);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param array<string,mixed> $dependencyList
     */
    protected function makeConsoleModuleTwo(
        string $commandDirectory = '',
        array $dependencyList = [],
        string $commandRootPath = '',
    ): ConsoleModule {

        return new ConsoleModuleTwo($commandDirectory, $dependencyList, $commandRootPath);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param array<string,mixed> $dependencyList
     */
    protected function makeFcModuleOne(
        string $commandNamespace = '',
        array $dependencyList = []
    ): FcModule {
        return new FcModuleOne($commandNamespace, $dependencyList);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param array<string,mixed> $dependencyList
     */
    protected function makeFcModuleTwo(
        string $commandNamespace = '',
        array $dependencyList = []
    ): FcModule {
        return new FcModuleTwo($commandNamespace, $dependencyList);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param array<string,mixed> $dependencyList
     */
    protected function makeMvcModuleOne(
        HttpMethod $method = HttpMethod::ANY,
        string $routePath = '/',
        string $routeAction = '',
        array $dependencyList = []
    ): MvcModule {
        return new MvcModuleOne(
            $method,
            $routePath,
            $routeAction,
            $dependencyList
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param array<string,mixed> $dependencyList
     */
    protected function makeMvcModuleTwo(
        HttpMethod $method = HttpMethod::ANY,
        string $routePath = '/',
        string $routeAction = '',
        array $dependencyList = []
    ): MvcModule {
        return new MvcModuleTwo(
            $method,
            $routePath,
            $routeAction,
            $dependencyList
        );
    }

    /** @param array<string,mixed> $dependencyList */
    protected function makeMvcModuleMinimal(
        HttpMethod $method = HttpMethod::ANY,
        string $routePath = '/',
        string $routeAction = '',
        array $dependencyList = []
    ): MvcModule {
        $dependencyList = array_merge(
            [
                Session::class => new MemorySession(),
                HttpFactory::class => $this->makeHttpFactory()
            ],
            $dependencyList
        );

        return $this->makeMvcModuleOne($method, $routePath, $routeAction, $dependencyList);
    }
}
