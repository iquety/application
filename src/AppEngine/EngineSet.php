<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

use Iquety\Application\Container;
use Throwable;

class EngineSet
{
    private ModuleSet $moduleSet;

    private array $engineList = [];

    public function __construct(private Container $container)
    {
        $this->moduleSet = new ModuleSet();
    }

    public function add(AppEngine $engine): void
    {
        $engine->useContainer($this->container);

        $this->engineList[] = $engine;
    }

    public function resolve(Input $input): ResponseDescriptor
    {
        try {
            foreach ($this->engineList as $engine) {
                $response = $engine->resolve($input, $this->moduleSet);
    
                if ($response !== null) {
                    return $response;
                }
            }
        } catch (Throwable $exception) {
            return new ResponseDescriptor(500, '');
        }
        

        return new ResponseDescriptor(404, '');
    }

    public function toArray(): array
    {
        return $this->engineList;
    }
}