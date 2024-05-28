<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpMethod;
use Iquety\Application\Http\HttpStatus;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class ApplicationRunTest extends ApplicationCase
{
    /** @test */
    public function runWithoutEngines(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No engine to handle the request');

        $instance = Application::instance();

        $instance->run();
    }

    /** @test */
    public function runWithoutMainModule(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No bootstrap specified for the application');

        $instance = Application::instance();

        $instance->bootEngine(new MvcEngine());

        $instance->run();
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function runBootFailed(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The bootApplication method failed');

        $instance = Application::instance();

        $instance->bootEngine(new MvcEngine());

        $instance->bootApplication(new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                throw new Exception('Boot dependencies failed');
            }

            public function bootRoutes(Router &$router): void
            {
            }
        });

        $instance->run();
    }

    /** @test */
    public function runSuccess(): void
    {
        $instance = Application::instance();

        $instance->container()->addSingleton(
            ServerRequestInterface::class,
            $this->makeServerRequest('/mvc-one/22', HttpMethod::ANY)
        );

        $instance->bootEngine(new MvcEngine());

        $instance->bootApplication($this->makeMvcBootstrapSessionDiactoros());

        $response = $instance->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertSame(HttpStatus::OK->value, $response->getStatusCode());
        $this->assertSame(
            'Resposta do controlador para id 22 input 0=22&id=22',
            (string)$response->getBody()
        );
    }
}
