<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController;

use InvalidArgumentException;
use Iquety\Application\AppEngine\FrontController\CommandHandler;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use Tests\AppEngine\FrontController\Support\FcBootstrapAlterDir;
use Tests\AppEngine\FrontController\Support\FcBootstrapConcrete;
use Tests\TestCase;

class FcOnlyBootTest extends TestCase
{
    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function bootInstanceException(string $httpFactoryContract): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('Invalid bootstrap. Required a %s', FcBootstrap::class)
        );

        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, FcEngine::class);

        /** @var Bootstrap $bootstrap */
        $bootstrap = $this->createMock(Bootstrap::class);

        $engine->boot($bootstrap);
    }

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function bootNamespace(string $httpFactoryContract): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, FcEngine::class);

        $engine->boot(new FcBootstrapConcrete());

        $handler = $this->appEngineContainer()->get(CommandHandler::class);

        $namespaces = $handler->namespaces();

        $bootstrapNamespace = 'Tests\AppEngine\FrontController\Support\FcBootstrapConcrete';
        $commandsNamespace = 'Tests\AppEngine\FrontController\Support\Commands';

        $this->assertArrayHasKey($bootstrapNamespace, $namespaces);
        $this->assertEquals($commandsNamespace, $namespaces[$bootstrapNamespace]);
    }

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function bootChangedCommandsDir(string $httpFactoryContract): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, FcEngine::class);

        $engine->boot(new FcBootstrapAlterDir());

        $handler = $this->appEngineContainer()->get(CommandHandler::class);

        $namespaces = $handler->namespaces();

        $bootstrapNamespace = 'Tests\AppEngine\FrontController\Support\FcBootstrapAlterDir';
        $commandsNamespace = 'Tests\AppEngine\FrontController\Support\AlterDir';

        $this->assertEquals($commandsNamespace, $namespaces[$bootstrapNamespace]);
    }

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function bootCommandsDirWithoutNamespace(string $httpFactoryContract): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, FcEngine::class);

        /** @var InvocationMocker */
        $bootstrap = $this->createMock(FcBootstrap::class);
        $bootstrap->method('commandsDirectory')->willReturn('AlterCommands');

        /** @var FcBootstrap $bootstrap */
        $engine->boot($bootstrap);

        $handler = $this->appEngineContainer()->get(CommandHandler::class);

        $namespaces = $handler->namespaces();

        $commandsNamespace = 'AlterCommands';

        $this->assertEquals($commandsNamespace, (string)current($namespaces));
    }
}
