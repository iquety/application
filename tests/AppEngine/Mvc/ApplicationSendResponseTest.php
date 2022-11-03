<?php

declare(strict_types=1);

namespace Tests\AppEngine\Mvc;

use Iquety\Application\Application;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Psr\Http\Message\ResponseInterface;
use Tests\AppEngine\Mvc\Support\UserStringClosureActionBootstrap;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class ApplicationSendResponseTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function sendResponse(): void
    {
        $this->expectOutputString('closure naitis');

        $app = Application::instance();
        $app->disableHeadersEmission();

        // a fabrica cria a intancia de ServerRequestInterface com o
        // URI apondando para '/user/33'
        $app->bootApplication(self::applicationBootstrapFactory('/user/33'));

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

        $app->sendResponse($response->withHeader('Content-type', 'text/html'));
    }
}