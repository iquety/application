<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Injection\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class EngineSet
{
    private array $engineList = [];

    public function add(AppEngine $engine): void
    {
        $this->engineList[] = $engine;
    }

    public function isEmpty(): bool
    {
        return $this->engineList !== [];
    }

    public function toArray(): array
    {
        return $this->engineList;
    }

    public function bootAllEngines(Container $container, Bootstrap $bootstrap): void
    {
        foreach ($this->engineList as $engine) {
            $engine->useContainer($container);

            $engine->boot($bootstrap);
        }
    }

    public function resolveRequest(
        ServerRequestInterface $request,
        ModuleSet $moduleSet,
        Application $application
    ): ?ResponseInterface {
        foreach ($this->engineList as $engine) {
            $response = $engine->execute($request, $moduleSet, $application);

            if ($response !== null) {
                return $response;
            }
        }

        return null;
    }
}