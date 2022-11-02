<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\CommandHandler;
use Iquety\Application\Application;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\Http\HttpResponseFactory;
use Psr\Http\Message\ServerRequestInterface;
use Tests\AppEngine\FrontController\Support\UserBootstrap;
use Tests\AppEngine\FrontController\Support\UserBootstrapAlterDir;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class CommandHandlerTest extends FrontControllerTestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function moduleBooted(): void
    {
        // a fabrica cria a intancia de ServerRequestInterface com o
        // URI apondando para 'user/command/4'
        $app = TestCase::applicationFactory('user/command/4');

        $handler = new FcEngine($app->container(), new HttpResponseFactory($app));
        $handler->boot(new UserBootstrap());
        $handler->boot(new UserBootstrapAlterDir());
        
        $namespaceList = $app->make(CommandHandler::class)->namespaces();

        $this->assertCount(2, $namespaceList);

        $this->assertEquals([
            UserBootstrap::class => $this->extractNamespace(UserBootstrap::class, '\Commands'),
            UserBootstrapAlterDir::class => $this->extractNamespace(UserBootstrapAlterDir::class, '\CommandsDir')
        ], $namespaceList);
    }

    /** @test */
    public function moduleExecuted(): void
    {
        // a fabrica cria a intancia de ServerRequestInterface com o
        // URI apondando para 'user/command/4'
        $app = TestCase::applicationFactory('user/command/4');

        $engine = new FcEngine($app->container(), new HttpResponseFactory($app));

        // UserBootstrap aponta para o diretório Commands
        $engine->boot(new UserBootstrap());

        // UserBootstrapAlterDir aponta para o diretório CommandsDir
        $engine->boot(new UserBootstrapAlterDir());
        
        /** @var CommandHandler $handler */
        $handler = $app->make(CommandHandler::class);

        $namespaceList = $handler->namespaces();
        $this->assertCount(2, $namespaceList);

        $this->assertEquals([
            UserBootstrap::class => $this->extractNamespace(UserBootstrap::class, '\Commands'),
            UserBootstrapAlterDir::class => $this->extractNamespace(UserBootstrapAlterDir::class, '\CommandsDir')
        ], $namespaceList);

        $moduleList = [
            UserBootstrap::class => new UserBootstrap(),
            UserBootstrapAlterDir::class => new UserBootstrapAlterDir()
        ];

        $response = $engine->execute(
            $app->make(ServerRequestInterface::class),
            $moduleList,
            fn($bootstrap) => $bootstrap->bootDependencies($app)
        );

        $this->assertFalse($handler->commandNotFound());
        $this->assertEquals(
            'Resposta do comando UserCommand',
            $response->getBody()->getContents()
        );
        $this->assertEquals(200, $response->getStatusCode());
    }
}
