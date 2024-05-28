<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

use InvalidArgumentException;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Injection\Container;
use RuntimeException;

/**
 * Armazena o conjunto de mecanismos disponíveis para responder às solicitações
 * mediante as entradas do usuário
 */
class EngineSet
{
    /** @var array<string,AppEngine> */
    private array $engineList = [];

    private ?AppEngine $mainEngine = null;

    public function __construct(
        private Container $container,
        private ModuleSet $moduleSet
    ) {
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

        if ($this->mainEngine === null) {
            $this->mainEngine = $engine;
        }
    }

    public function bootEnginesWith(Bootstrap $bootstrap): void
    {
        foreach ($this->engineList as $engine) {
            $engine->boot($bootstrap);
        }
    }

    public function sourceHandler(): SourceHandler
    {
        if ($this->mainEngine === null) {
            throw new RuntimeException(
                'To return the handler, you must add at least one engine'
            );
        }

        return $this->mainEngine->sourceHandler();
    }

    public function resolve(Input $input): ActionDescriptor
    {
        foreach ($this->engineList as $engine) {
            $response = $engine->resolve($input, $this->moduleSet);

            if ($response !== null) {
                return $response;
            }
        }

        return $this->sourceHandler()->getNotFoundDescriptor();
    }

    public function hasEngines(): bool
    {
        return $this->engineList !== [];
    }

    public function toArray(): array
    {
        return $this->engineList;
    }
}
