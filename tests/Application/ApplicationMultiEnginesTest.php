<?php

declare(strict_types=1);

namespace Tests\Application;

use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpStatus;
use Iquety\Application\Http\Session;
use Psr\Http\Message\ServerRequestInterface;
use Tests\Application\Support\FcBootMultiConcrete;
use Tests\Application\Support\MvcBootMultiConcrete;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class ApplicationMultiEnginesTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function runMvcPrecedence(string $httpFactoryContract): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);

        $app = Application::instance();

        $app->addSingleton(
            ServerRequestInterface::class,
            fn() => $httpFactory->createServerRequest('GET', 'multi/43')
        );

        $app->bootEngine(new MvcEngine()); // precedencia para o Mvc
        $app->bootEngine(new FcEngine());

        $bootstrap = $this->appBootstrapFactory(function (Application $app) use ($httpFactoryContract) {
            $app->addSingleton(Session::class, MemorySession::class);
            $app->addSingleton(HttpFactory::class, $httpFactoryContract);
        });

        $app->bootApplication($bootstrap);

        $app->bootModule(new FcBootMultiConcrete());
        $app->bootModule(new MvcBootMultiConcrete());

        $response = $app->run();

        $this->assertSame('Resposta do controlador para id 43 input 43', (string)$response->getBody());
        $this->assertSame(HttpStatus::OK, $response->getStatusCode());
    }

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function runFcPrecedence(string $httpFactoryContract): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);

        $app = Application::instance();

        $app->addSingleton(
            ServerRequestInterface::class,
            fn() => $httpFactory->createServerRequest('GET', 'multi/43')
        );

        $app->bootEngine(new FcEngine()); // precedencia para o FrontController
        $app->bootEngine(new MvcEngine()); 
        

        $bootstrap = $this->appBootstrapFactory(function (Application $app) use ($httpFactoryContract) {
            $app->addSingleton(Session::class, MemorySession::class);
            $app->addSingleton(HttpFactory::class, $httpFactoryContract);
        });

        $app->bootApplication($bootstrap);

        $app->bootModule(new FcBootMultiConcrete());
        $app->bootModule(new MvcBootMultiConcrete());

        $response = $app->run();

        $this->assertSame('Resposta do comando para id 43', (string)$response->getBody());
        $this->assertSame(HttpStatus::OK, $response->getStatusCode());
    }
}
