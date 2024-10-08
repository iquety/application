<?php

declare(strict_types=1);

namespace Tests\IoEngine\Console;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Console\ConsoleDescriptor;
use Iquety\Application\IoEngine\Console\ConsoleModule;
use Iquety\Application\IoEngine\Console\ConsoleSourceHandler;
use Iquety\Application\IoEngine\Console\NotImplementedException;
use Iquety\Application\IoEngine\Console\RoutineSource;
use Iquety\Application\IoEngine\Console\RoutineSourceSet;
use Iquety\Application\IoEngine\Module;
use Iquety\Injection\Container;
use Tests\TestCase;

class ConsoleSourceHandlerTest extends TestCase
{
    /** @test */
    public function sources(): void
    {
        $routineSourceSet = new RoutineSourceSet(Module::class);
        $routineSourceSet->add(new RoutineSource(__DIR__ . '/a'));
        $routineSourceSet->add(new RoutineSource(__DIR__ . '/b'));

        $handler = new ConsoleSourceHandler();

        $this->assertFalse($handler->hasSources());

        $handler->addSources($routineSourceSet);

        $this->assertSame([
            __DIR__ . '/a',
            __DIR__ . '/b',
        ], $handler->getDirectoryList());

        $this->assertTrue($handler->hasSources());
    }

    /** @test */
    public function commandName(): void
    {
        $handler = new ConsoleSourceHandler();

        $handler->setCommandName('nome-comando');

        $this->assertSame('nome-comando', $handler->getCommandName());
    }

    /** @test */
    public function commandPath(): void
    {
        $handler = new ConsoleSourceHandler();

        $handler->setCommandPath(__DIR__);

        $this->assertSame(__DIR__, $handler->getCommandPath());
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function makeDescriptor(): void
    {
        $routineSourceSet = new RoutineSourceSet(Module::class);
        $routineSourceSet->add(new RoutineSource(__DIR__ . '/a'));
        $routineSourceSet->add(new RoutineSource(__DIR__ . '/b'));

        $handler = new ConsoleSourceHandler();

        $handler->addSources($routineSourceSet);

        /** @var ConsoleDescriptor $descriptor */
        $descriptor = $handler->getDescriptorTo(Input::fromString('/'));

        $this->assertInstanceOf(ConsoleDescriptor::class, $descriptor);
        $this->assertSame('', $descriptor->output());
        $this->assertSame(0, $descriptor->status());
    }

    /** @return array<string,array<int,string>> */
    public function gettersProvider(): array
    {
        $list = [];

        $list['getErrorDescriptor'] = ['getErrorDescriptor'];
        $list['getMainDescriptor'] = ['getMainDescriptor'];
        $list['getNotFoundDescriptor'] = ['getNotFoundDescriptor'];

        return $list;
    }

    /** @return array<string,array<int,string>> */
    public function settersProvider(): array
    {
        $list = [];

        $list['setErrorActionClass'] = ['setErrorActionClass'];
        $list['setMainActionClass'] = ['setMainActionClass'];
        $list['setNotFoundActionClass'] = ['setNotFoundActionClass'];

        return $list;
    }

    /**
     * @test
     * @dataProvider gettersProvider
     */
    public function gettersNotImplemented(string $methodName): void
    {
        $this->expectException(NotImplementedException::class);
        $this->expectExceptionMessage(
            'The Console engine does not use this method'
        );

        $handler = new ConsoleSourceHandler();
        ;

        $handler->{$methodName}();
    }

    /**
     * @test
     * @dataProvider settersProvider
     */
    public function settersNotImplemented(string $methodName): void
    {
        $this->expectException(NotImplementedException::class);
        $this->expectExceptionMessage(
            'The Console engine does not use this method'
        );

        $handler = new ConsoleSourceHandler();

        $handler->{$methodName}('qualquer coisa');
    }

    // private function makeConsoleModule(): ConsoleModule
    // {
    //     return new class extends ConsoleModule
    //     {
    //         public function bootDependencies(Container $container): void
    //         {
    //             // ...
    //         }

    //         public function bootRoutineDirectories(RoutineSourceSet &$sourceSet): void
    //         {
    //             $sourceSet->add(new RoutineSource(__DIR__ . '/Console'));
    //         }

    //         public function getCommandName(): string
    //         {
    //             return 'test-script';
    //         }

    //         /** Devolve o diretório real da aplicação que implementa o Console */
    //         public function getCommandPath(): string
    //         {
    //             return __DIR__;
    //         }
    //     };
    // }
}
