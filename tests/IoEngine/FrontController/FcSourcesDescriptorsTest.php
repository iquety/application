<?php

declare(strict_types=1);

namespace Tests\IoEngine\FrontController;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\ActionDescriptor;
use Iquety\Application\IoEngine\FrontController\Command\Command;
use Iquety\Application\IoEngine\FrontController\FcSourceHandler;
use Tests\IoEngine\FrontController\Stubs\AnyCommand;
use Tests\IoEngine\FrontController\Stubs\NotCommand;
use Tests\TestCase;

class FcSourcesDescriptorsTest extends TestCase
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
        $handler = new FcSourceHandler();

        $handler->{$setterMethod}(AnyCommand::class);

        $descriptor = $handler->{$getterMethod}();

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        $this->assertSame($identity, $descriptor->module());
        $this->assertSame(Command::class, $descriptor->type());
        $this->assertSame(AnyCommand::class . '::execute', $descriptor->action());
    }

    /**
     * @test
     * @dataProvider settersProvider
     */
    public function invalidCustomActionClass(string $setterMethod): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Class %s is not a valid command',
            NotCommand::class
        ));

        $handler = new FcSourceHandler();

        $handler->{$setterMethod}(NotCommand::class);
    }

    /**
     * @test
     * @dataProvider settersProvider
     */
    public function invalidAbstractCommand(string $setterMethod): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Class %s is not a valid command',
            Command::class
        ));

        $handler = new FcSourceHandler();

        $handler->{$setterMethod}(Command::class);
    }
}
