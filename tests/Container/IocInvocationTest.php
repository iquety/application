<?php

declare(strict_types=1);

namespace Tests\Container;

use ArrayObject;
use Freep\Application\Container\Container;
use Freep\Application\Container\ContainerException;
use Freep\Application\Container\InversionOfControl;
use Freep\Application\Container\NotFoundException;
use stdClass;
use Tests\Support\ContainerIoc;
use Tests\Support\ContainerIocNoConstructor;
use Tests\TestCase;

/** @codeCoverageIgnore */
class ContainerIocInvocationTest extends TestCase
{
    /** @test */
    public function injectDependencyInConstructor(): void
    {
        $container = new Container();
        $container->registerDependency(ArrayObject::class, fn() => new ArrayObject());
        $container->registerDependency(stdClass::class, fn() => new stdClass());

        $control = new InversionOfControl($container);
        
        // o método ContainerIoc->values devolve um array com os valores setados
        // no construtor __construct(ArrayObject $object, stdClass $class = null)
        $results = $control->resolve(ContainerIoc::class . "::values");
        $this->assertInstanceOf(ArrayObject::class, $results[0]);
        $this->assertInstanceOf(stdClass::class, $results[1]);
    }

    /** @test */
    public function injectNotFoundDependencyInConstructor(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            "It was not possible to resolve the value for parameter (\$object) in method (__construct)"
        );

        $control = new InversionOfControl(new Container());
        $control->resolve(ContainerIoc::class . "::values");
    }

    /** @test */
    public function containerException(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage("Impossible to inject string dependency");

        $control = new InversionOfControl(new Container());
        $control->resolve("values");
    }

    /** @return array<int,mixed> */
    public function methodsInvocationProvider(): array
    {
        return [
            [ ContainerIocNoConstructor::class . "::injectedMethod" ],
            [ ContainerIoc::class . "::injectedMethod" ],
            [ ContainerIoc::class . "::injectedStaticMethod" ],
            [ array(ContainerIoc::class, "injectedMethod") ],
            [ array(new ContainerIoc(new ArrayObject()), "injectedMethod") ],
            [ new ContainerIoc(new ArrayObject()) ], // __invoke(ArrayObject $o)
            [ fn(ArrayObject $object) => $object->getArrayCopy() ],
            [ "declaredFunction" ],
        ];
    }

    /**
     * @test
     * @dataProvider methodsInvocationProvider
     * @param mixed $caller
    */
    public function runMethod($caller): void
    {
        include_once __DIR__ . '/../Support/ContainerIocFunction.php';

        $container = new Container();
        $container->registerDependency(ArrayObject::class, fn() => new ArrayObject(['x']));

        $control = new InversionOfControl($container);
        $value = $control->resolve($caller); // <- injeta ArrayObject como argumento
        $this->assertEquals([ 'x' ], $value);
    }

    /** @test */
    public function injectNotFoundDependencyInMethod(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            "It was not possible to resolve the value for parameter (\$object) in method (injectedMethod)"
        );

        $control = new InversionOfControl(new Container());
        $control->resolve(ContainerIocNoConstructor::class . "::injectedMethod");
    }
}
