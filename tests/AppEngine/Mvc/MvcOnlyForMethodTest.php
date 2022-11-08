<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController;

use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpMethod;
use Iquety\Application\Http\HttpStatus;
use Iquety\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;
use Tests\AppEngine\Mvc\Support\Controllers\UserControllerForMethod;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class MvcOnlyForMethodTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @return array<string,array[int,mixed]> */
    public function controllerCasesProvider(): array
    {
        $factoryList = $this->httpFactoryProvider();

        $methodList = HttpMethod::all();

        array_shift($methodList); // remove o ANY

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
     * @dataProvider controllerCasesProvider
     */
    public function controllerOk(string $httpFactoryContract, string $method): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, MvcEngine::class);
        $request = $this->requestFactory($httpFactory, 'user/42', $method);

        // os comandos usam a instância de Application para fabricar dependências
        $app = Application::instance();
        $app->addSingleton(HttpFactory::class, fn() => $httpFactory);
        $app->addSingleton(ServerRequestInterface::class, fn() => $request);
        $app->addSingleton('forMethod', fn() => $method);

        /** @var InvocationMocker */
        $bootstrap = $this->createMock(MvcBootstrap::class);
        $bootstrap->method('bootRoutes')->will($this->returnCallback(function (Router $router) {
            $router->any('/user/:id')
                ->usingAction(UserControllerForMethod::class . '::create');
        }));

        /** @var MvcBootstrap $bootstrap */
        $engine->boot($bootstrap);

        $response = $engine->execute(
            $request,
            [$bootstrap::class => &$bootstrap],
            function (MvcBootstrap $bootstrap) {
            }
        );

        $this->assertStringContainsString(
            'Resposta do controlador para id 42 input 42',
            (string)$response->getBody()
        );
        $this->assertEquals(HttpStatus::OK, $response->getStatusCode());
    }

    /**
     * @test
     * @dataProvider controllerCasesProvider
     */
    public function commandMethodAnyOk(string $httpFactoryContract, string $method): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, MvcEngine::class);
        $request = $this->requestFactory($httpFactory, 'user/42', $method);

        // os comandos usam a instância de Application para fabricar dependências
        $app = Application::instance();
        $app->addSingleton(HttpFactory::class, fn() => $httpFactory);
        $app->addSingleton(ServerRequestInterface::class, fn() => $request);
        $app->addSingleton('forMethod', fn() => HttpMethod::ANY);

        /** @var InvocationMocker */
        $bootstrap = $this->createMock(MvcBootstrap::class);
        $bootstrap->method('bootRoutes')->will($this->returnCallback(function (Router $router) {
            $router->any('/user/:id')
                ->usingAction(UserControllerForMethod::class . '::create');
        }));

        /** @var MvcBootstrap $bootstrap */
        $engine->boot($bootstrap);

        $response = $engine->execute(
            $request,
            [$bootstrap::class => &$bootstrap],
            function (MvcBootstrap $bootstrap) {
            }
        );

        $this->assertStringContainsString(
            'Resposta do controlador para id 42 input 42',
            (string)$response->getBody()
        );

        $this->assertEquals(HttpStatus::OK, $response->getStatusCode());
    }

    /**
     * @test
     * @dataProvider controllerCasesProvider
     */
    public function commandMethodBlocked(string $httpFactoryContract, string $method): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, MvcEngine::class);
        $request = $this->requestFactory($httpFactory, 'user/42', $method);

        $forMethod = HttpMethod::GET;

        if ($method === HttpMethod::GET) {
            $forMethod = HttpMethod::POST;
        }

        // os comandos usam a instância de Application para fabricar dependências
        $app = Application::instance();
        $app->addSingleton(HttpFactory::class, fn() => $httpFactory);
        $app->addSingleton(ServerRequestInterface::class, fn() => $request);
        $app->addFactory('forMethod', fn() => $forMethod);

        /** @var InvocationMocker */
        $bootstrap = $this->createMock(MvcBootstrap::class);
        $bootstrap->method('bootRoutes')->will($this->returnCallback(function (Router $router) {
            $router->any('/user/:id')
                ->usingAction(UserControllerForMethod::class . '::create');
        }));

        /** @var MvcBootstrap $bootstrap */
        $engine->boot($bootstrap);

        $response = $engine->execute(
            $request,
            [$bootstrap::class => &$bootstrap],
            function (MvcBootstrap $bootstrap) {
            }
        );

        $this->assertNull($response);
    }
}
