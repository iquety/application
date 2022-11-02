<?php

declare(strict_types=1);

namespace Tests\AppEngine\Mvc;

use Iquety\Application\Application;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Tests\AppEngine\Mvc\Support\UserArrayClosureActionBootstrap;
use Tests\AppEngine\Mvc\Support\UserBootstrap;
use Tests\AppEngine\Mvc\Support\UserNoActionBootstrap;
use Tests\AppEngine\Mvc\Support\UserNullClosureActionBootstrap;
use Tests\AppEngine\Mvc\Support\UserStringClosureActionBootstrap;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class ApplicationRunTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function runForNotFoundRoute(): void
    {
        // a fabrica cria a intancia de ServerRequestInterface com o
        // URI apondando para '/user/33'
        $app = TestCase::applicationFactory();

        $app->addEngine(MvcEngine::class);

        $response = $app->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /** @test */
    public function runForNoActionRoute(): void
    {
        // a fabrica cria a intancia de ServerRequestInterface com o
        // URI apondando para '/user/33'
        $app = TestCase::applicationFactory();

        $app->addEngine(MvcEngine::class);

        // O bootstrap do módulo cria uma rota apontando para o
        // padrão '/user/:id'
        $app->bootModule(new UserNoActionBootstrap());

        // as dependencias do módulo só serão resolvidas se
        // a rota '/user/:id' bater com a requisição efetuada para '/user/33'
        $response = $app->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('The route found does not have a action', $response->getBody()->getContents());
    }

    /** @test */
    public function runForClosureActionRoute(): void
    {
        // a fabrica cria a intancia de ServerRequestInterface com o
        // URI apondando para '/user/33'
        $app = TestCase::applicationFactory();

        $app->addEngine(MvcEngine::class);

        // O bootstrap do módulo cria uma rota apontando para o
        // padrão '/user/:id'
        $app->bootModule(new UserStringClosureActionBootstrap());

        // as dependencias do módulo só serão resolvidas se
        // a rota '/user/:id' bater com a requisição efetuada para '/user/33'
        $response = $app->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('closure naitis', $response->getBody()->getContents());
    }

    /** @test */
    public function runForNullClosureActionRoute(): void
    {
        // a fabrica cria a intancia de ServerRequestInterface com o
        // URI apondando para '/user/33'
        $app = TestCase::applicationFactory();

        $app->addEngine(MvcEngine::class);

        // O bootstrap do módulo cria uma rota apontando para o
        // padrão '/user/:id'
        $app->bootModule(new UserNullClosureActionBootstrap());

        // as dependencias do módulo só serão resolvidas se
        // a rota '/user/:id' bater com a requisição efetuada para '/user/33'
        $response = $app->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('', $response->getBody()->getContents());
    }

    /** @test */
    public function runForArrayClosureActionRoute(): void
    {
        // a fabrica cria a intancia de ServerRequestInterface com o
        // URI apondando para '/user/33'
        $app = TestCase::applicationFactory();

        $app->addEngine(MvcEngine::class);

        // O bootstrap do módulo cria uma rota apontando para o
        // padrão '/user/:id'
        $app->bootModule(new UserArrayClosureActionBootstrap());

        // as dependencias do módulo só serão resolvidas se
        // a rota '/user/:id' bater com a requisição efetuada para '/user/33'
        $response = $app->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"name":"naitis"}', $response->getBody()->getContents());
    }

    /** @test */
    public function runForController(): void
    {
        // a fabrica cria a intancia de ServerRequestInterface com o
        // URI apondando para '/user/33'
        $app = TestCase::applicationFactory();

        $app->addEngine(MvcEngine::class);

        // O bootstrap do módulo cria uma rota apontando para o
        // padrão '/user/:id'
        $app->bootModule(new UserBootstrap());

        // as dependencias do módulo só serão resolvidas se
        // a rota '/user/:id' bater com a requisição efetuada para '/user/33'
        $response = $app->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('/user/33 - ID: 33', $response->getBody()->getContents());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
