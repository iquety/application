<?php

declare(strict_types=1);

namespace Tests\MvcEngine;

use InvalidArgumentException;
use Iquety\Application\IoEngine\ActionDescriptor;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;
use Iquety\Application\IoEngine\Mvc\MvcSourceHandler;
use Tests\IoEngine\Stubs\Mvc\AnyController;
use Tests\IoEngine\Stubs\Mvc\NotController;
use Tests\TestCase;

class MvcSourcesDescriptorsTest extends TestCase
{
    /** @return array<string,array<int,mixed>> */
    public function settersProvider(): array
    {
        $list = [];

        $list['error descriptor'] = ['setErrorActionClass', 'getErrorDescriptor', 'error'];
        $list['main descriptor'] = ['setMainActionClass', 'getMainDescriptor', 'main'];
        $list['not found descriptor'] = ['setNotFoundActionClass', 'getNotFoundDescriptor', 'not-found'];

        return $list;
    }

    /**
     * @test
     * @dataProvider settersProvider
     */
    public function getCustomDescriptor(string $setterMethod, string $getterMethod, string $identity): void
    {
        $handler = new MvcSourceHandler();

        $handler->{$setterMethod}(AnyController::class);

        $descriptor = $handler->{$getterMethod}();

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        $this->assertSame($identity, $descriptor->module());
        $this->assertSame(Controller::class, $descriptor->type());
        $this->assertSame(AnyController::class . '::execute', $descriptor->action());
    }

    /**
     * @test
     * @dataProvider settersProvider
     */
    public function invalidCustomActionClass(string $setterMethod): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Class %s is not a valid controller',
            NotController::class
        ));

        $handler = new MvcSourceHandler();

        $handler->{$setterMethod}(NotController::class);
    }

    /**
     * @test
     * @dataProvider settersProvider
     */
    public function invalidAbstractController(string $setterMethod): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Class %s is not a valid controller',
            Controller::class
        ));

        $handler = new MvcSourceHandler();

        $handler->{$setterMethod}(Controller::class);
    }
}
