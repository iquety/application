<?php

declare(strict_types=1);

namespace Tests\Container;

use ArrayObject;
use Freep\Application\Container\Container;
use Freep\Application\Container\ContainerException;
use Freep\Application\Container\InversionOfControl;
use Freep\Application\Container\NotFoundException;
use stdClass;
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
        $results = $control->resolve(ImplContainerIoc::class . "::values");
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
        $control->resolve(ImplContainerIoc::class . "::values");
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
            [ ImplContainerIocNoConstructor::class . "::injectedMethod" ],
            [ ImplContainerIoc::class . "::injectedMethod" ],
            [ ImplContainerIoc::class . "::injectedStaticMethod" ],
            [ array(ImplContainerIoc::class, "injectedMethod") ],
            [ array(new ImplContainerIoc(new ArrayObject()), "injectedMethod") ],
            [ new ImplContainerIoc(new ArrayObject()) ], // __invoke(ArrayObject $o)
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
        include_once 'ImplContainerIocFunction.php';

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
        $control->resolve(ImplContainerIocNoConstructor::class . "::injectedMethod");
    }
}
