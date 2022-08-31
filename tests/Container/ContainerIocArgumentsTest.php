<?php

declare(strict_types=1);

namespace Tests\Container;

use ArrayObject;
use Freep\Application\Container\Container;
use Freep\Application\Container\NotFoundException;
use Tests\TestCase;

/** @codeCoverageIgnore */
class ContainerIocArgumentsTest extends TestCase
{
    /** @test */
    public function runMethodWithRequiredArguments(): void
    {
        $container = new Container();
        $container->registerDependency(ArrayObject::class, fn() => new ArrayObject(['x']));

        $value = $container->inversionOfControl(
            ImplContainerIoc::class . "::injectedMethodExtraArguments", // <- injeta ArrayObject
            [ "id" => "1", "name" => "Ricardo"] // <- acrescenta  $id + $name
        );
        $this->assertSame([ 'x', 1, "Ricardo" ], $value);
    }

    /** @test */
    public function runMethodWithoutRequiredArguments(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            "It was not possible to resolve the value for parameter (\$name) in method (injectedMethodExtraArguments)"
        );

        $container = new Container();
        $container->registerDependency(ArrayObject::class, fn() => new ArrayObject(['x']));

        $container->inversionOfControl(
            ImplContainerIoc::class . "::injectedMethodExtraArguments", // <- injeta ArrayObject
            [ "id" => "1", ] // <- acrescenta  $id, mas esquece do $name
        );
    }

    /** @return array<int,array> */
    public function valuedArgumentsProvider(): array
    {
        return [
            [ array("id" => "1", "name" => "Pereira"), array( 1, "Pereira") ],
            [ array("id" => "1"), array( 1, "Ricardo") ],

            [ array("id" => 1, "name" => "Pereira"), array( 1, "Pereira") ],
            [ array("id" => 1), array( 1, "Ricardo") ],

            [ array("name" => "Pereira"), array( 33, "Pereira") ],
            [ array(), array( 33, "Ricardo") ],
        ];
    }

    /**
     * @test
     * @dataProvider valuedArgumentsProvider
     * @param array<string,string> $arguments
     * @param array<int,mixed> $values
    */
    public function runMethodWithDefaultValueArguments(array $arguments, array $values): void
    {
        $container = new Container();
        $container->registerDependency(ArrayObject::class, fn() => new ArrayObject(['x']));

        $value = $container->inversionOfControl(
            ImplContainerIoc::class . "::injectedMethodExtraDefaultValueArguments", // <- injeta ArrayObject
            $arguments // <- acrescenta  $id + $name
        );

        $this->assertSame(array_merge(["x"], $values), $value);
    }
}
