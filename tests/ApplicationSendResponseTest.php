<?php

declare(strict_types=1);

namespace Tests;

use Freep\Application\Application;
use Psr\Http\Message\ResponseInterface;
use ReflectionObject;
use RuntimeException;
use Tests\Support\UserArrayClosureActionBootstrap;
use Tests\Support\UserBootstrap;
use Tests\Support\UserClosureActionBootstrap;
use Tests\Support\UserNoActionBootstrap;
use Tests\Support\UserNullClosureActionBootstrap;
use Tests\Support\UserRestrictedBootstrap;
use Tests\Support\UserStringClosureActionBootstrap;

use function xdebug_get_headers;

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

        // a fabrica cria a intancia de ServerRequestInterface com o
        // URI apondando para '/user/33'
        $app = TestCase::applicationFactory();

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
