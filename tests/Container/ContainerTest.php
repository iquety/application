<?php

declare(strict_types=1);

namespace Tests\Container;

use ArrayObject;
use Exception;
use Freep\Application\Container\Container;
use Freep\Application\Container\ContainerException;
use Freep\Application\Container\NotFoundException;
use Tests\TestCase;

/** @codeCoverageIgnore */
class ContainerTest extends TestCase
{
    /** @test */
    public function hasId(): void
    {
        $container = new Container();
        $this->assertFalse($container->has('id'));
        $container->registerDependency('id', 'parangarikotirimirruaro');
        $this->assertTrue($container->has('id'));

        $container = new Container();
        $this->assertFalse($container->has('id'));
        $container->registerSingletonDependency('id', 'parangarikotirimirruaro');
        $this->assertTrue($container->has('id'));
    }

    /** @test */
    public function hasIdPrecedenceSharedBefore(): void
    {
        $container = new Container();

        // registra uma dependencia compartilhada
        $container->registerSingletonDependency('id', 'parangarikotirimirruaro');

        // obtém sa listas de dependencias registradas
        $sharedValue = $this->getPropertyValue($container, 'singleton');
        $factoryValue = $this->getPropertyValue($container, 'factory');
        $this->assertTrue(in_array('parangarikotirimirruaro', $sharedValue));
        $this->assertCount(0, $factoryValue);

        // tenta registrar uma dependencia não-compartilhada com mesmo $id
        $container->registerDependency('id', 'sobrescreve');
        
        // valor foi sobrescrito na dependencia compartilhada
        // não é possível "descompartilhar"
        $sharedValue = $this->getPropertyValue($container, 'singleton');
        $factoryValue = $this->getPropertyValue($container, 'factory');
        $this->assertTrue(in_array('sobrescreve', $sharedValue));
        $this->assertCount(0, $factoryValue);
    }

    /** @test */
    public function hasIdPrecedenceSharedAfter(): void
    {
        $container = new Container();

        // registra uma dependencia compartilhada
        $container->registerDependency('id', 'parangarikotirimirruaro');

        // obtém sa listas de dependencias registradas
        $sharedValue = $this->getPropertyValue($container, 'singleton');
        $factoryValue = $this->getPropertyValue($container, 'factory');
        $this->assertCount(0, $sharedValue);
        $this->assertTrue(in_array('parangarikotirimirruaro', $factoryValue));

        // tenta registrar uma dependencia não-compartilhada com mesmo $id
        $container->registerSingletonDependency('id', 'sobrescreve');
        
        // dependência é removida da fabricação e alocada como compartilhada
        $sharedValue = $this->getPropertyValue($container, 'singleton');
        $factoryValue = $this->getPropertyValue($container, 'factory');
        $this->assertTrue(in_array('sobrescreve', $sharedValue));
        $this->assertCount(0, $factoryValue);
    }

    /** @test */
    public function setIdAndValue(): void
    {
        $container = new Container();
        $container->registerDependency('id', 'parangarikotirimirruaro');
        $this->assertEquals('parangarikotirimirruaro', $container->get('id'));

        $container = new Container();
        $container->registerDependency('id', fn() => "kkk");
        $this->assertEquals('kkk', $container->get('id'));

        $container = new Container();
        $container->registerDependency('id', "ArrayObject");
        $this->assertInstanceOf(ArrayObject::class, $container->get('id'));
    }

    /** @test */
    public function setIdOnly(): void
    {
        $container = new Container();
        $container->registerDependency('id');
        $this->assertEquals('id', $container->get('id'));

        $container = new Container();
        $container->registerDependency(ArrayObject::class);
        $this->assertEquals(new ArrayObject(), $container->get(ArrayObject::class));
    }

    /** @test */
    public function getShared(): void
    {
        $container = new Container();
        $container->registerSingletonDependency('id', fn() => microtime());

        // a mesma instância é chamada
        $this->assertEquals($container->get('id'), $container->get('id'));
    }

    /** @test */
    public function getFactory(): void
    {
        $container = new Container();
        $container->registerDependency('id', fn() => microtime());

        // a instância é fabricada a cada chamada
        $this->assertNotEquals($container->get('id'), $container->get('id'));
    }

    /** @test */
    public function singleResolution(): void
    {
        $container = new Container();
        $container->registerSingletonDependency(ArrayObject::class);

        /** @var ArrayObject<int, string> */
        $retrieveOne = $container->get(ArrayObject::class);
        $this->assertEquals([], $retrieveOne->getArrayCopy());

        // muda o estado da dependencia
        $retrieveOne->append("abc");

        /** @var ArrayObject<int, string> */
        $retrieveTwo = $container->get(ArrayObject::class);
        $this->assertEquals([ 'abc' ], $retrieveTwo->getArrayCopy());
    }

    /** @test */
    public function notFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find dependency definition for not-exists');

        $container = new Container();
        $container->get('not-exists');
    }

    /** @test */
    public function resolveId(): void
    {
        $container = new Container();
        $container->registerDependency('closure', fn() => microtime());

        $this->assertNotSame(
            $container->get('closure'),
            $container->get('closure')
        );

        $container = new Container();
        $container->registerSingletonDependency('closure', fn() => microtime());
    }

    /** @test */
    public function resolveException(): void
    {
        $this->expectException(ContainerException::class);

        $container = new Container();
        $container->registerDependency('closure', fn() => throw new Exception());
        $container->get('closure');
    }
}
