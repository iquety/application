<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\Command\CommandDescriptor;
use Iquety\Application\AppEngine\FrontController\Command\ErrorCommand;
use Iquety\Application\AppEngine\FrontController\Command\MainCommand;
use Iquety\Application\AppEngine\FrontController\Command\NotFoundCommand;
use Iquety\Application\AppEngine\FrontController\Directory;
use Iquety\Application\AppEngine\FrontController\DirectorySet;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\FrontController\SourceHandler;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\Application;
use Iquety\Injection\Container;
use Iquety\Injection\NotFoundException;
use Tests\Unit\AppEngine\FrontController\Stubs\Commands\OneCommand;
use Tests\Unit\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class FcEngineTest extends TestCase
{
    /** @test */
    public function bootEngineDefaults(): void
    {
        $container = new Container();

        $engine = new FcEngine();

        $engine->useContainer($container);

        $engine->boot(new class extends FcBootstrap {
            public function bootDependencies(Application $app): void
            {}
        });

        /** @var SourceHandler */
        $handler = $container->get(SourceHandler::class);
        
        $sourceList = $handler->getSourceList();
        $this->assertCount(1, $sourceList);

        $directorySet = $sourceList[0];
        $this->assertCount(0, $directorySet->toArray());

        $this->assertEquals(
            new CommandDescriptor('', ErrorCommand::class, []),
            $handler->getErrorDescriptor()
        );

        $this->assertEquals(
            new CommandDescriptor('', MainCommand::class, []),
            $handler->getMainDescriptor()
        );

        $this->assertEquals(
            new CommandDescriptor('', NotFoundCommand::class, []),
            $handler->getNotFoundDescriptor()
        );
    }

    /** @test */
    public function bootEngineCustom(): void
    {
        $container = new Container();

        $engine = new FcEngine();

        $engine->useContainer($container);

        $engine->boot(new class extends FcBootstrap {
            public function bootDependencies(Application $app): void
            {}

            public function bootDirectories(DirectorySet &$directorySet): void
            {
                $directorySet->add(new Directory(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
                    __DIR__ . "/Stubs/Commands"
                ));
            }

            public function getErrorCommandClass(): string
            {
                return OneCommand::class;
            }

            public function getNotFoundCommandClass(): string
            {
                return OneCommand::class;
            }

            public function getMainCommandClass(): string
            {
                return OneCommand::class;
            }
        });

        /** @var SourceHandler */
        $handler = $container->get(SourceHandler::class);
        
        $sourceList = $handler->getSourceList();
        $this->assertCount(1, $sourceList);

        $directorySet = $sourceList[0];
        $this->assertCount(1, $directorySet->toArray());

        $expectedDirectory = new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
            __DIR__ . "/Stubs/Commands"
        );

        $actualDirectory = $directorySet
            ->toArray()[$expectedDirectory->getIdentity()];

        $this->assertSame(
            $expectedDirectory->getIdentity(),
            $actualDirectory->getIdentity()
        );
        
        $this->assertEquals(
            new CommandDescriptor('', OneCommand::class, []),
            $handler->getErrorDescriptor()
        );

        $this->assertEquals(
            new CommandDescriptor('', OneCommand::class, []),
            $handler->getMainDescriptor()
        );

        $this->assertEquals(
            new CommandDescriptor('', OneCommand::class, []),
            $handler->getNotFoundDescriptor()
        );
    }

    /** @test */
    public function bootNotFrontController(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'Could not find dependency definition for ' . SourceHandler::class
        );

        $container = new Container();

        $engine = new FcEngine();

        $engine->useContainer($container);

        $engine->boot(new class extends MvcBootstrap {
            public function bootDependencies(Application $app): void
            {}
        });

        // se o bootstrap não for FcBootstrap, a dependência 
        // SourceHandler não existirá no container

        /** @var SourceHandler */
        $container->get(SourceHandler::class);
    }

    /** @test */
    public function bootTwoEngines(): void
    {
        $this->markTestIncomplete('Testar o boot de vários engines ao mesmo tempo');
    }
}
