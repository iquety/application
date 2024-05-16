<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

use InvalidArgumentException;
use Iquety\Injection\Container;
use Throwable;

/**
 * Armazena o conjunto de mecanismos disponíveis para responder às solicitações 
 * mediante as entradas do usuário
 */
class EngineSet
{
    private ModuleSet $moduleSet;

    /** @var array<string,AppEngine> */
    private array $engineList = [];

    public function __construct(private Container $container)
    {
        $this->moduleSet = new ModuleSet();
    }

    public function add(AppEngine $engine): void
    {
        $engine->useContainer($this->container);

        if (isset($this->engineList[$engine::class]) === true) {
            throw new InvalidArgumentException(
                'The same engine cannot be added twice'
            );
        }

        $this->engineList[$engine::class] = $engine;
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