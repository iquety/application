<?php

declare(strict_types=1);

namespace Freep\Application\Container;

use Closure;
use Freep\Application\Container\ContainerException;
use Freep\Application\Container\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use Throwable;

class Container implements ContainerInterface
{
    public const RESOLVE_FACTORY = 'factory';

    public const RESOLVE_SINGLETON = 'singleton';

    /** @var array<string, Closure|string> */
    private array $factory = [];

    /** @var array<string, Closure|string> */
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

    /**
     * @throws NotFoundException
     * @return mixed
     */
    public function get(string $id)
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
     * @throws ContainerException
     * @return mixed
     */
    private function resolveSingleton(string $id)
    {
        if (isset($this->singleton[$id]) === false) {
            return null;
        }

        if ($this->singletonResolved[$id] === false) {
            $this->singleton[$id] = $this->resolve($id, self::RESOLVE_SINGLETON);
            $this->singletonResolved[$id] = true;
        }

        return $this->singleton[$id];
    }

    /**
     * @throws ContainerException
     * @return mixed
     */
    private function resolveFactory(string $id)
    {
        return $this->resolve($id, self::RESOLVE_FACTORY);
    }

    /**
     * @throws ContainerException
     * @return mixed
     */
    private function resolve(string $id, string $type = Container::RESOLVE_SINGLETON)
    {
        $registereds = ($type === self::RESOLVE_FACTORY)
            ? $this->factory
            : $this->singleton;

        $resolved = $registereds[$id];

        try {
            if (is_callable($registereds[$id]) === true) {
                $resolved = call_user_func($registereds[$id]);
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

    /**
     * Invoca um objeto ou classe através do container.
     * string: Controller::action
     * array: [Controller, action]
     * object: new Controller()
     * callable: "Controller" ou function() {}
     * @param string|array<string,string>|object|callable $callable
     * @param array<string,mixed> $arguments
     * @return mixed
     */
    public function inversionOfControl(string|array|object|callable $callable, array $arguments = [])
    {
        if (is_string($callable) === true && strpos($callable, '::') !== false) {
            $callable = explode('::', $callable);
        }

        if (is_array($callable) === true) {
            $objectOrClass = $callable[0];
            $methodName    = $callable[1];
            return $this->invokeClass($objectOrClass, $methodName, $arguments);
        }

        if (is_object($callable) === true) {
            /** @var object $callable */
            return $this->invokeMethod($callable, '__invoke', $arguments);
        }

        if (is_callable($callable) === false) {
            throw new ContainerException("Impossible to inject " . gettype($callable) . " dependency");
        }

        return $this->invokeFunction(\Closure::fromCallable($callable), $arguments);
    }

    /**
     * @param object|class-string $objectOrClass
     * @param array<string,mixed> $arguments
     * @return mixed
    */
    private function invokeClass(object|string $objectOrClass, string $methodName, array $arguments)
    {
        // injeta dependencias no construtor
        $reflector = new ReflectionClass($objectOrClass);
        $construct = $reflector->getConstructor();

        $resolution = $construct === null
            ? new $objectOrClass()
            : $reflector->newInstanceArgs($this->argumentsInjected($construct, $arguments));

        return $this->invokeMethod($resolution, $methodName, $arguments);
    }

    /**
     * @param array<string,mixed> $arguments
     * @return mixed
    */
    private function invokeMethod(object $object, string $method, array $arguments)
    {
        $reflection = new ReflectionMethod($object, $method);

        if ($reflection->isStatic() === true) {
            $object = null;
        }

        return $reflection->invokeArgs(
            $object,
            $this->argumentsInjected($reflection, $arguments)
        );
    }

    /**
     * @param array<string,mixed> $arguments
     * @return mixed
    */
    private function invokeFunction(Closure $function, array $arguments)
    {
        $reflection = new ReflectionFunction($function);
        return $reflection->invokeArgs(
            $this->argumentsInjected($reflection, $arguments)
        );
    }

    /**
     * @param array<string,mixed> $arguments
     * @return array<int,mixed>
    */
    private function argumentsInjected(ReflectionMethod|ReflectionFunction $reflection, array $arguments): array
    {
        $reflect = function (ReflectionParameter $param) use ($reflection, $arguments) {
            $name = $param->getName();
            $type = $param->getType();

            // argumentos passados coincidem com parâmetros declarados no método
            if (isset($arguments[$name]) === true) {
                return $arguments[$name];
            }

            $dependencyId = (string) $type;
            $dependencyId = ltrim($dependencyId, '?');
            if ($type !== null && $this->has($dependencyId) === true) {
                return $this->get($dependencyId);
            }

            if ($param->isDefaultValueAvailable() === true) {
                return $param->getDefaultValue();
            }

            throw new NotFoundException(sprintf(
                'It was not possible to resolve the value for parameter ($%s) in method (%s)',
                $name,
                $reflection->getName()
            ));
        };

        return array_map($reflect, $reflection->getParameters());
    }
}
