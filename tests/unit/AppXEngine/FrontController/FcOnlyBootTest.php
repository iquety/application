<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\CommandHandler;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use RuntimeException;
use Tests\AppEngine\FrontController\Support\FcBootstrapAlterDir;
use Tests\AppEngine\FrontController\Support\FcBootstrapConcrete;
use Tests\Unit\TestCase;

class FcOnlyBootTest extends TestCase
{
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

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function bootstrapWithoutRegisterDirectories(string $httpFactoryContract): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("No directories registered as command source");

        $httpFactory = $this->httpFactory($httpFactoryContract);

        $engine = $this->appEngineFactory($httpFactory, FcEngine::class);

        $request = $this->requestFactory($httpFactory);
        $moduleList = [];
        $bootDependencies = fn() => null;

        $response = $engine->execute($request, $moduleList, $bootDependencies);

        $this->assertNull($response);
    }
}
