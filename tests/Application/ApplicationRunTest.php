<?php

declare(strict_types=1);

namespace Tests\Application;

use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\AppEngine\MemoryEngine;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpStatus;
use Iquety\Application\Http\Session;
use RuntimeException;
use Tests\Application\Support\ErrorExceptionEngine;
use Tests\Application\Support\NotFoundEngine;
use Tests\Application\Support\ServerErrorEngine;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class ApplicationRunTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function runWithoutEngine(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No web engine to handle the request');

        $app = Application::instance();

        $app->run();
    }

    /** @test */
    public function runWithoutBootstrap(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No bootstrap specified for the application');

        $app = Application::instance();

        $app->addEngine(MemoryEngine::class);

        $app->run();
    }

    /** @test */
    public function runWithoutSessionDependency(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Could not find dependency definition for %s', Session::class)
        );

        $app = Application::instance();

        $app->addEngine(MemoryEngine::class);

        $app->bootApplication($this->appBootstrapFactory(function(Application $app){

        }));

        $app->run();
    }

    /** @test */
    public function runWithInvalidSessionDependency(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Please implement %s dependency for %s->bootApplication',
                Session::class,
                Application::class
            )
        );

        $app = Application::instance();

        $app->addEngine(MemoryEngine::class);

        $app->bootApplication($this->appBootstrapFactory(function(Application $app){
            $app->addSingleton(Session::class, fn() => (object)[]);
        }));

        $app->run();
    }

    /** @test */
    public function runWithoutHttpFactoryDependency(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Could not find dependency definition for %s', HttpFactory::class)
        );

        $app = Application::instance();

        $app->addEngine(MemoryEngine::class);

        $app->bootApplication($this->appBootstrapFactory(function(Application $app){
            $app->addSingleton(Session::class, MemorySession::class);
        }));

        $app->run();
    }

    /** @test */
    public function runWithInvalidHttpFactoryDependency(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Please implement %s dependency for %s->bootApplication',
                HttpFactory::class,
                Application::class
            )
        );

        $app = Application::instance();

        $app->addEngine(MemoryEngine::class);

        $app->bootApplication($this->appBootstrapFactory(function(Application $app){
            $app->addSingleton(Session::class, MemorySession::class);
            $app->addSingleton(HttpFactory::class, fn() => (object)[]);
        }));

        $app->run();
    }

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function runOk(string $httpFactory): void
    {
        $app = Application::instance();

        $app->addEngine(MemoryEngine::class);

        $bootstrap = $this->appBootstrapFactory(function(Application $app) use ($httpFactory){
            $app->addSingleton(Session::class, MemorySession::class);
            $app->addSingleton(HttpFactory::class, $httpFactory);
        });

        $app->bootApplication($bootstrap);

        $response = $app->run();

        $this->assertInstanceOf(Application::class, $app->make(Application::class));
        $this->assertSame(HttpStatus::HTTP_OK, $response->getStatusCode());
        $this->assertSame('', (string)$response->getBody());
    }

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function runNotFound(string $httpFactory): void
    {
        $app = Application::instance();

        $app->addEngine(NotFoundEngine::class);

        $bootstrap = $this->appBootstrapFactory(function(Application $app) use ($httpFactory){
            $app->addSingleton(Session::class, MemorySession::class);
            $app->addSingleton(HttpFactory::class, $httpFactory);
        });

        $app->bootApplication($bootstrap);

        $response = $app->run();

        $this->assertInstanceOf(Application::class, $app->make(Application::class));
        $this->assertSame(HttpStatus::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertSame('', (string)$response->getBody());
    }

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function runException(string $httpFactory): void
    {
        $app = Application::instance();

        $app->addEngine(ErrorExceptionEngine::class);

        $bootstrap = $this->appBootstrapFactory(function(Application $app) use ($httpFactory){
            $app->addSingleton(Session::class, MemorySession::class);
            $app->addSingleton(HttpFactory::class, $httpFactory);
        });

        $app->bootApplication($bootstrap);

        $response = $app->run();

        $this->assertInstanceOf(Application::class, $app->make(Application::class));
        $this->assertSame(HttpStatus::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertSame('Error exception', (string)$response->getBody());
    }

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function runServerError(string $httpFactory): void
    {
        $app = Application::instance();

        $app->addEngine(ErrorExceptionEngine::class); // todo

        $bootstrap = $this->appBootstrapFactory(function(Application $app) use ($httpFactory){
            $app->addSingleton(Session::class, MemorySession::class);
            $app->addSingleton(HttpFactory::class, $httpFactory);
        });

        $app->bootApplication($bootstrap);

        $response = $app->run();

        $this->assertInstanceOf(Application::class, $app->make(Application::class));
        $this->assertSame(HttpStatus::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertSame('Error exception', (string)$response->getBody());
    }
}
