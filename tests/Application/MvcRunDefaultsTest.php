<?php

declare(strict_types=1);

namespace Tests\Application;

use Exception;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\Mvc\Controller\ErrorController;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpMethod;
use Iquety\Application\Http\HttpMime;
use Iquety\Application\Http\HttpStatus;
use Iquety\Application\Http\Session;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Tests\AppEngine\Mvc\Stubs\OneController;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class MvcRunDefaultsTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    public function tearDown(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function runHome(): void
    {
        $instance = Application::instance();

        $instance->container()->addSingleton(
            ServerRequestInterface::class,
            $this->makeServerRequest('/', HttpMethod::ANY)
        );

        $instance->bootEngine(new MvcEngine());

        $instance->bootApplication($this->makeBootstrap());

        $response = $instance->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertSame(HttpStatus::OK->value, $response->getStatusCode());
        $this->assertSame(
            'Iquety Framework - Home Page',
            (string)$response->getBody()
        );
    }

    /** @test */
    public function runNotFound(): void
    {
        $instance = Application::instance();

        $instance->container()->addSingleton(
            ServerRequestInterface::class,
            $this->makeServerRequest('/not-found', HttpMethod::ANY)
        );

        $instance->bootEngine(new MvcEngine());

        $instance->bootApplication($this->makeBootstrap());

        $response = $instance->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertSame(HttpStatus::NOT_FOUND->value, $response->getStatusCode());
        $this->assertSame(
            'Not Found',
            (string)$response->getBody()
        );
    }

    /** @test */
    public function runError(): void
    {
        $instance = Application::instance();

        $instance->container()->addSingleton(
            ServerRequestInterface::class,
            $this->makeServerRequest('/error-test/33', HttpMethod::ANY)
        );

        $instance->bootEngine(new MvcEngine());

        $instance->bootApplication($this->makeBootstrap());

        $response = $instance->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);

        // $this->assertSame(HttpStatus::INTERNAL_SERVER_ERROR->value, $response->getStatusCode());
        $this->assertSame(
            'Erro lancado',
            (string)$response->getBody()
        );
    }

    protected function makeServerRequest(string $path = ''): ServerRequestInterface
    {
        $httpFactory = new DiactorosHttpFactory();

        $request = $httpFactory->createRequestFromGlobals();

        $request = $request->withAddedHeader('Accept', HttpMime::HTML->value);

        if ($path === '') {
            return $request;
        }

        $request = $request->withMethod(HttpMethod::GET->value);
        
        return $request->withUri(
            $httpFactory->createUri("http://localhost/" . trim($path, '/'))
        );
    }

    protected function makeBootstrap(): MvcBootstrap
    {
        return new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton(Session::class, MemorySession::class);
                $container->addSingleton(HttpFactory::class, new DiactorosHttpFactory());
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/error-test/:id')->usingAction(ErrorController::class, 'execute');
            }
        };
    }
}
