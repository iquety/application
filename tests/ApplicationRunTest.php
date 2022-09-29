<?php

declare(strict_types=1);

namespace Tests;

use ArrayObject;
use Freep\Application\Application;
use Freep\Application\Bootstrap;
use Freep\Application\Http\Request;
use Freep\Application\Http\Response;
use Freep\Application\Routing\Router;
use Laminas\Diactoros\Uri;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Tests\Support\UserBootstrap;
use Tests\Support\UserRestrictedBootstrap;

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

        $response = $app->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /** @test */
    public function runForAccesDeniedRoute(): void
    {
        // a fabrica cria a intancia de ServerRequestInterface com o
        // URI apondando para '/user/33'
        $app = TestCase::applicationFactory();

        // O bootstrap do módulo cria uma rota apontando para o
        // padrão '/user/:id'
        $app->bootModule(new UserRestrictedBootstrap());

        // as dependencias do módulo só serão resolvidas se
        // a rota '/user/:id' bater com a requisição efetuada para '/user/33'
        $app->run();

        $response = $app->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function runForController(): void
    {
        // a fabrica cria a intancia de ServerRequestInterface com o
        // URI apondando para '/user/33'
        $app = TestCase::applicationFactory();

        // O bootstrap do módulo cria uma rota apontando para o
        // padrão '/user/:id'
        $app->bootModule(new UserBootstrap());

        // as dependencias do módulo só serão resolvidas se
        // a rota '/user/:id' bater com a requisição efetuada para '/user/33'
        $app->run();

        $response = $app->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('/user/33 - ID: 33', $response->getBody()->getContents());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
