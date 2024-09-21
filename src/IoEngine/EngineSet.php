<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Injection\Container;
use RuntimeException;

/**
 * Armazena o conjunto de mecanismos disponíveis para responder às solicitações
 * mediante as entradas do usuário
 */
class EngineSet
{
    /** @var array<string,IoEngine> */
    private array $engineList = [];

    private ?IoEngine $mainEngine = null;

    public function __construct(private Container $container)
    {
    }

    public function add(IoEngine $engine): void
    {
        if (isset($this->engineList[$engine::class]) === true) {
            throw new InvalidArgumentException(sprintf(
                'Engine %s has already been registered',
                $engine::class
            ));
        }

        $engine->useContainer($this->container);

        $this->engineList[$engine::class] = $engine;

        if ($this->mainEngine === null) {
            $this->mainEngine = $engine;
        }
    }

    public function bootEnginesWith(Module $module): void
    {
        foreach ($this->engineList as $engine) {
            $engine->boot($module);
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
            $response = $engine->resolve($input);

            if ($response !== null) {
                return $response;
            }
        }

        // disparar um NotFoundException para o executor capiturar
        return $this->sourceHandler()->getNotFoundDescriptor();
    }

    public function hasEngines(): bool
    {
        return $this->engineList !== [];
    }

    /** @return array<string,IoEngine> */
    public function toArray(): array
    {
        return $this->engineList;
    }
}
