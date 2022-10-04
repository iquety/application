<?php

declare(strict_types=1);

namespace Freep\Application\Container;

use Closure;
use Freep\Application\Container\ContainerException;
use Freep\Application\Container\NotFoundException;
use Psr\Container\ContainerInterface;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class Container implements ContainerInterface
{
    public const RESOLVE_FACTORY = 'factory';

    public const RESOLVE_SINGLETON = 'singleton';

    /** @var array<string,Closure|string> */
    private array $factory = [];

    /** @var array<string,Closure|string> */
    private array $singleton = [];

    /** @var array<string, bool> */
    private array $singletonResolved = [];

    /**
     * @param string $id
     * @param Closure|string $value
     */
    public function registerDependency(string $id, Closure|string $value = null): void
    {
        // dependência singleton é sempre singleton
        if (isset($this->singleton[$id])) {
            $this->singleton[$id] = $value ?? $id;
            return;
        }

        // se $id for a assinatura de uma classe, registra-a diretamente
        $this->factory[$id] = $value ?? $id;
    }

    /**
     * @param string $id
     * @param Closure|string $value
     */
    public function registerSingletonDependency(string $id, Closure|string $value = null): void
    {
        if (isset($this->factory[$id]) === true) {
            unset($this->factory[$id]);
        }

        // se $id for a assinatura de uma classe, registra-a diretamente
        $this->singleton[$id] = $value ?? $id;
        $this->singletonResolved[$id] = false;
    }

    /** @throws NotFoundException */
    public function get(string $id): mixed
    {
        if ($this->has($id) === false) {
            throw new NotFoundException(sprintf('Could not find dependency definition for %s', $id));
        }

        $singletonObject = $this->resolveSingleton($id);
        if ($singletonObject !== null) {
            return $singletonObject;
        }

        return $this->resolveFactory($id);
    }

    /**
     * @param array<int,mixed> $arguments
     * @throws NotFoundException
     */
    public function getWithArguments(string $id, array $arguments): mixed
    {
        if ($this->has($id) === false) {
            throw new NotFoundException(sprintf('Could not find dependency definition for %s', $id));
        }

        $singletonObject = $this->resolveSingleton($id, $arguments);
        if ($singletonObject !== null) {
            return $singletonObject;
        }

        return $this->resolveFactory($id, $arguments);
    }

    /**
     * @param array<int,mixed> $arguments
     * @throws ContainerException
     */
    private function resolveSingleton(string $id, array $arguments = []): mixed
    {
        if (isset($this->singleton[$id]) === false) {
            return null;
        }

        if ($this->singletonResolved[$id] === false) {
            $this->singleton[$id] = $this->resolve($id, self::RESOLVE_SINGLETON, $arguments);
            $this->singletonResolved[$id] = true;
        }

        return $this->singleton[$id];
    }

    /**
     * @param array<int,mixed> $arguments
     * @throws ContainerException
     */
    private function resolveFactory(string $id, array $arguments = []): mixed
    {
        return $this->resolve($id, self::RESOLVE_FACTORY, $arguments);
    }

    /**
     * @param array<int,mixed> $arguments
     * @throws ContainerException
     */
    private function resolve(
        string $id,
        string $type = Container::RESOLVE_SINGLETON,
        array $arguments = []
    ): mixed {
        $registereds = ($type === self::RESOLVE_FACTORY)
            ? $this->factory
            : $this->singleton;

        $resolved = $registereds[$id];

        try {
            if (is_callable($registereds[$id]) === true) {
                $resolved = call_user_func_array($registereds[$id], $arguments);
            }
        } catch (Throwable $exception) {
            throw new ContainerException($exception->getMessage(), $exception->getCode(), $exception);
        }

        // assinatura de uma classe
        if (is_string($registereds[$id]) && class_exists($registereds[$id]) === true) {
            $resolved = new $registereds[$id]();
        }

        // se não for um callable ou assinatura de classe, retorna uma string
        return $resolved;
    }

    public function has(string $id): bool
    {
        return isset($this->factory[$id]) || isset($this->singleton[$id]);
    }
}
