<?php

declare(strict_types=1);

namespace Tests\Application;

use Exception;
use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Application\Http\HttpStatus;
use Iquety\Application\Http\Session;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
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

        $app->bootEngine($this->createMock(AppEngine::class));

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

        $app->bootEngine($this->createMock(AppEngine::class));

        $app->bootApplication($this->appBootstrapFactory(function (Application $app) {
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

        $app->bootEngine($this->createMock(AppEngine::class));

        $app->bootApplication($this->appBootstrapFactory(function (Application $app) {
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

        $app->bootEngine($this->createMock(AppEngine::class));

        $app->bootApplication($this->appBootstrapFactory(function (Application $app) {
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

        $app->bootEngine($this->createMock(AppEngine::class));

        $app->bootApplication($this->appBootstrapFactory(function (Application $app) {
            $app->addSingleton(Session::class, MemorySession::class);
            $app->addSingleton(HttpFactory::class, fn() => (object)[]);
        }));

        $app->run();
    }

    /** @test */
    public function runWithExceptionMainBootstrap(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The bootApplication method failed');

        $app = Application::instance();

        $app->bootEngine($this->createMock(AppEngine::class));

        $app->bootApplication(new class implements Bootstrap {
            public function bootDependencies(Application $app): void
            {
                throw new Exception('Teste');
            }
        });

        $app->run();
    }

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function runOk(string $httpFactory): void
    {
        $app = Application::instance();

        /** @var InvocationMocker */
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(HttpStatus::OK);

        /** @var InvocationMocker */
        $engine = $this->createMock(AppEngine::class);
        $engine->method('execute')->willReturn($response);

        /** @var AppEngine $engine */
        $app->bootEngine($engine);

        $app->bootEngine($this->createMock(AppEngine::class));

        $bootstrap = $this->appBootstrapFactory(function (Application $app) use ($httpFactory) {
            $app->addSingleton(Session::class, MemorySession::class);
            $app->addSingleton(HttpFactory::class, $httpFactory);
        });

        $app->bootApplication($bootstrap);

        $response = $app->run();

        $this->assertInstanceOf(Application::class, $app->make(Application::class));
        
        $this->assertInstanceOf(HttpFactory::class, $app->make(HttpFactory::class));

        $this->assertInstanceOf(
            ServerRequestInterface::class,
            $app->make(ServerRequestInterface::class)
        );

        $this->assertInstanceOf(
            StreamInterface::class,
            $app->make(StreamInterface::class, 'texto qualquer')
        );

        $this->assertInstanceOf(
            UriInterface::class,
            $app->make(UriInterface::class, 'http://localhost/user/naitis')
        );

        $this->assertInstanceOf(
            ResponseInterface::class,
            $app->make(ResponseInterface::class, 404, HttpStatus::NOT_FOUND)
        );
        $this->assertInstanceOf(
            HttpResponseFactory::class,
            $app->make(HttpResponseFactory::class)
        );
        
        $this->assertSame(HttpStatus::OK, $response->getStatusCode());
    }

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function runNotFound(string $httpFactory): void
    {
        $app = Application::instance();

        /** @var InvocationMocker */
        $engine = $this->createMock(AppEngine::class);
        $engine->method('execute')->willReturn(null);

        /** @var AppEngine $engine */
        $app->bootEngine($engine);

        $bootstrap = $this->appBootstrapFactory(function (Application $app) use ($httpFactory) {
            $app->addSingleton(Session::class, MemorySession::class);
            $app->addSingleton(HttpFactory::class, $httpFactory);
        });

        $app->bootApplication($bootstrap);

        $response = $app->run();

        $this->assertInstanceOf(Application::class, $app->make(Application::class));
        $this->assertSame(HttpStatus::NOT_FOUND, $response->getStatusCode());
        $this->assertSame('', (string)$response->getBody());
    }

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function runException(string $httpFactory): void
    {
        $app = Application::instance();

        /** @var InvocationMocker */
        $engine = $this->createMock(AppEngine::class);
        $engine->method('execute')->will($this->throwException(new Exception('Error exception')));

        /** @var AppEngine $engine */
        $app->bootEngine($engine);

        $bootstrap = $this->appBootstrapFactory(function (Application $app) use ($httpFactory) {
            $app->addSingleton(Session::class, MemorySession::class);
            $app->addSingleton(HttpFactory::class, $httpFactory);
        });

        $app->bootApplication($bootstrap);

        $response = $app->run();

        $this->assertInstanceOf(Application::class, $app->make(Application::class));
        $this->assertSame(HttpStatus::INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertStringContainsString(
            'Error: Error exception on file',
            (string)$response->getBody()
        );
    }

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function runServerError(string $httpFactory): void
    {
        $app = Application::instance();

        $callback = function () {
            trigger_error('Error triggered');
        };

        /** @var InvocationMocker */
        $engine = $this->createMock(AppEngine::class);
        $engine->method('execute')->will($this->returnCallback($callback));

        /** @var AppEngine $engine */
        $app->bootEngine($engine);

        $bootstrap = $this->appBootstrapFactory(function (Application $app) use ($httpFactory) {
            $app->addSingleton(Session::class, MemorySession::class);
            $app->addSingleton(HttpFactory::class, $httpFactory);
        });

        $app->bootApplication($bootstrap);

        $response = $app->run();

        $this->assertInstanceOf(Application::class, $app->make(Application::class));
        $this->assertSame(HttpStatus::INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertStringContainsString(
            'Error: Error triggered on file',
            (string)$response->getBody()
        );
    }
}
