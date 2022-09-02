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

class InversionOfControl
{
    public function __construct(private ContainerInterface $container)
    {
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
    public function resolve(string|array|object|callable $callable, array $arguments = [])
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
            if ($type !== null && $this->container->has($dependencyId) === true) {
                return $this->container->get($dependencyId);
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
