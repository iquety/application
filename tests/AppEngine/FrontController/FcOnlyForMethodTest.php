<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpMethod;
use Iquety\Application\Http\HttpStatus;
use Psr\Http\Message\ServerRequestInterface;
use Tests\AppEngine\FrontController\Support\FcBootstrapConcrete;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class FcOnlyForMethodTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    public function commandCasesProvider(): array
    {
        $factoryList = $this->httpFactoryProvider();

        $methodList = [
            // HttpMethod::ANY,
            HttpMethod::GET,
            HttpMethod::POST,
            HttpMethod::PUT,
            HttpMethod::PATCH,
            HttpMethod::DELETE,
        ];

        $list = [];

        foreach ($factoryList as $name => $row) {
            foreach ($methodList as $method) {
                $list["$name with method $method"] = [ $row[0], $method ];
            }
        }

        return $list;
    }

    /**
     * @test
     * @dataProvider commandCasesProvider
     */
    public function commandOk(string $httpFactoryContract, string $method): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, FcEngine::class);
        $request = $this->requestFactory($httpFactory, 'user/command/42', $method);

        // os comandos usam Application para fabricar as dependências abaixo
        $app = Application::instance();
        $app->addSingleton(HttpFactory::class, fn() => $httpFactory);
        $app->addSingleton(ServerRequestInterface::class, fn() => $request);
        $app->addSingleton('forMethod', fn() => $method);

        $bootstrap = new FcBootstrapConcrete();

        $engine->boot($bootstrap);

        $response = $engine->execute(
            $request,
            [$bootstrap::class => &$bootstrap],
            function (FcBootstrap $bootstrap) {
            }
        );

        $this->assertEquals(HttpStatus::OK, $response->getStatusCode());
        $this->assertStringContainsString(
            'Resposta do comando para id 42',
            (string)$response->getBody()
        );
    }

    /**
     * @test
     * @dataProvider commandCasesProvider
     */
    public function commandMethodAnyOk(string $httpFactoryContract, string $method): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, FcEngine::class);
        $request = $this->requestFactory($httpFactory, 'user/command/42', $method);

        // os comandos usam Application para fabricar as dependências abaixo
        $app = Application::instance();
        $app->addSingleton(HttpFactory::class, fn() => $httpFactory);
        $app->addSingleton(ServerRequestInterface::class, fn() => $request);
        $app->addSingleton('forMethod', fn() => HttpMethod::ANY);

        $bootstrap = new FcBootstrapConcrete();

        $engine->boot($bootstrap);

        $response = $engine->execute(
            $request,
            [$bootstrap::class => &$bootstrap],
            function (FcBootstrap $bootstrap) {
            }
        );

        $this->assertEquals(HttpStatus::OK, $response->getStatusCode());
        $this->assertStringContainsString(
            'Resposta do comando para id 42',
            (string)$response->getBody()
        );
    }

    /**
     * @test
     * @dataProvider commandCasesProvider
     */
    public function commandMethodBlocked(string $httpFactoryContract, string $method): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, FcEngine::class);
        $request = $this->requestFactory($httpFactory, 'user/command/42', $method);

        $forMethod = HttpMethod::GET;

        if ($method === HttpMethod::GET) {
            $forMethod = HttpMethod::POST;
        }

        // os comandos usam Application para fabricar as dependências abaixo
        $app = Application::instance();
        $app->addSingleton(HttpFactory::class, fn() => $httpFactory);
        $app->addSingleton(ServerRequestInterface::class, fn() => $request);
        $app->addFactory('forMethod', fn() => $forMethod);

        $bootstrap = new FcBootstrapConcrete();

        $engine->boot($bootstrap);

        $response = $engine->execute(
            $request,
            [$bootstrap::class => &$bootstrap],
            function (FcBootstrap $bootstrap) {
            }
        );

        $this->assertNull($response);
    }
}
