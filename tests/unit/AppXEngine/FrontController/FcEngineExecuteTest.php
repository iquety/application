<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\AppEngine\FrontController\Directory;
use Iquety\Application\AppEngine\FrontController\DirectorySet;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\Session;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Tests\Unit\AppEngine\FrontController\Stubs\Commands\ErrorCommand;
use Tests\Unit\AppEngine\FrontController\Stubs\Commands\MainCommand;
use Tests\Unit\AppEngine\FrontController\Stubs\Commands\NotFoundCommand;
use Tests\Unit\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class FcEngineExecuteTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function commandHome(): void
    {
        $application = Application::instance();

        $applicationBootstrap = new class extends FcBootstrap {
            public function bootDependencies(Application $app): void
            {
                $app->addSingleton(Session::class, MemorySession::class);
                $app->addSingleton(HttpFactory::class, DiactorosHttpFactory::class);
            }

            public function bootDirectories(DirectorySet &$directorySet): void
            {
                $directorySet->add(new Directory(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
                    __DIR__ . "/Stubs/Commands"
                ));
            }
        };

        $applicationBootstrap->bootDependencies($application);

        $moduleSet = new ModuleSet();
        $moduleSet->add($applicationBootstrap);

        $engine = new FcEngine();
        $engine->useContainer($application->container());
        $engine->boot($applicationBootstrap);

        $response = $engine->execute(
            $this->makeServerRequest('/'),
            $moduleSet,
            $application
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Iquety Framework', (string)$response->getBody());
    }

    public function commandsProvider(): array
    {
        $list = [];

        $list['empty'] = ['', 200, ''];
        $list['/'] = ['/', 200, ''];
        // $list['/?a=11&b=22'] = ['/?a=11&b=22', 200];

        $list['not-exists'] = ['not-exists', 404, '0=not-exists'];
        $list['/not-exists'] = ['/not-exists', 404, '0=not-exists'];
        $list['not-exists/'] = ['not-exists/', 404, '0=not-exists'];
        $list['/not-exists/'] = ['/not-exists/', 404, '0=not-exists'];

        $list['not-exists?a=11'] = ['not-exists?a=11', 404, '0=not-exists&a=11'];
        $list['/not-exists?a=11'] = ['/not-exists?a=11', 404, '0=not-exists&a=11'];
        $list['not-exists/?a=11'] = ['not-exists/?a=11', 404, '0=not-exists&a=11'];
        $list['/not-exists/?a=11'] = ['/not-exists/?a=11', 404, '0=not-exists&a=11'];

        $list['not-exists/x'] = ['not-exists/x', 404, '0=not-exists&1=x'];
        $list['/not-exists/x'] = ['/not-exists/x', 404, '0=not-exists&1=x'];
        $list['not-exists/x/'] = ['not-exists/x/', 404, '0=not-exists&1=x'];
        $list['/not-exists/x/'] = ['/not-exists/x/', 404, '0=not-exists&1=x'];

        $list['one-command'] = ['one-command', 201, ''];
        $list['/one-command'] = ['/one-command', 201, ''];
        $list['one-command/'] = ['one-command/', 201, ''];
        $list['/one-command/'] = ['/one-command/', 201, ''];

        $list['one-command'] = ['one-command?a=11&b=22', 201, ''];
        $list['/one-command'] = ['/one-command?a=11&b=22', 201, ''];
        $list['one-command/'] = ['one-command/?a=11&b=22', 201, ''];
        $list['/one-command/'] = ['/one-command/?a=11&b=22', 201, ''];
        
        $list['one-command/p'] = ['one-command/x', 201, '0=x'];
        $list['/one-command/p'] = ['/one-command/x', 201, '0=x'];
        $list['one-command/p/'] = ['one-command/x/', 201, '0=x'];
        $list['/one-command/p/'] = ['/one-command/x/', 201, '0=x'];

        $list['one-command/p'] = ['one-command/x?a=11&b=22', 201, '0=x'];
        $list['/one-command/p'] = ['/one-command/x?a=11&b=22', 201, '0=x'];
        $list['one-command/p/'] = ['one-command/x/?a=11&b=22', 201, '0=x'];
        $list['/one-command/p/'] = ['/one-command/x/?a=11&b=22', 201, '0=x'];

        $list['one-command/p/one/test'] = ['one-command/x/y/z', 201, '0=x&1=y&2=z'];
        $list['/one-command/p/one/test'] = ['/one-command/x/y/z', 201, '0=x&1=y&2=z'];
        $list['one-command/p/one/test/'] = ['one-command/x/y/z/', 201, '0=x&1=y&2=z'];
        $list['/one-command/p/one/test/'] = ['/one-command/x/y/z/', 201, '0=x&1=y&2=z'];

        $list['one-command/p/one/test'] = ['one-command/x/y/z?a=11&b=22', 201, '0=x&1=y&2=z'];
        $list['/one-command/p/one/test'] = ['/one-command/x/y/z?a=11&b=22', 201, '0=x&1=y&2=z'];
        $list['one-command/p/one/test/'] = ['one-command/x/y/z/?a=11&b=22', 201, '0=x&1=y&2=z'];
        $list['/one-command/p/one/test/'] = ['/one-command/x/y/z/?a=11&b=22', 201, '0=x&1=y&2=z'];

        return $list;
    }

    /**
     * @test
     * @dataProvider commandsProvider
     */
    public function commandExists(string $uri, int $status, string $params): void
    {
        $application = Application::instance();

        $applicationBootstrap = new class extends FcBootstrap {
            public function bootDependencies(Application $app): void
            {
                $app->addSingleton(Session::class, MemorySession::class);
                $app->addSingleton(HttpFactory::class, DiactorosHttpFactory::class);
            }

            public function bootDirectories(DirectorySet &$directorySet): void
            {
                $directorySet->add(new Directory(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
                    __DIR__ . "/Stubs/Commands"
                ));
            }

            public function getErrorCommandClass(): string
            {
                return ErrorCommand::class;
            }

            public function getNotFoundCommandClass(): string
            {
                return NotFoundCommand::class;
            }

            public function getMainCommandClass(): string
            {
                return MainCommand::class;
            }
        };

        $applicationBootstrap->bootDependencies($application);

        $moduleSet = new ModuleSet();
        $moduleSet->add($applicationBootstrap);

        $engine = new FcEngine();
        $engine->useContainer($application->container());
        $engine->boot($applicationBootstrap);

        $response = $engine->execute(
            $this->makeServerRequest($uri),
            $moduleSet,
            $application
        );

        $this->assertSame($status, $response->getStatusCode());
        $this->assertSame($params, (string)$response->getBody());
    }

    /** @test */
    public function commandExistsMultiple(): void
    {
        $application = Application::instance();

        $applicationBootstrap = new class extends FcBootstrap {
            public function bootDependencies(Application $app): void
            {
                $app->addSingleton(Session::class, MemorySession::class);
                $app->addSingleton(HttpFactory::class, DiactorosHttpFactory::class);
            }

            public function bootDirectories(DirectorySet &$directorySet): void
            {
                $directorySet->add(new Directory(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
                    __DIR__ . "/Stubs/Commands"
                ));
            }
        };

        $applicationBootstrap->bootDependencies($application);

        $moduleSet = new ModuleSet();
        $moduleSet->add($applicationBootstrap);

        $engine = new FcEngine();
        $engine->useContainer($application->container());
        $engine->boot($applicationBootstrap);

        $response = $engine->execute(
            $this->makeServerRequest('one-command/one/two'),
            $moduleSet,
            $application
        );

        $this->assertSame(201, $response->getStatusCode());
    }

    /** @test */
    public function commandNotExists(): void
    {
        $application = Application::instance();

        $applicationBootstrap = new class extends FcBootstrap {
            public function bootDependencies(Application $app): void
            {
                $app->addSingleton(Session::class, MemorySession::class);
                $app->addSingleton(HttpFactory::class, DiactorosHttpFactory::class);
            }

            public function bootDirectories(DirectorySet &$directorySet): void
            {
                $directorySet->add(new Directory(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
                    __DIR__ . "/Stubs/Commands"
                ));
            }
        };

        $applicationBootstrap->bootDependencies($application);

        $moduleSet = new ModuleSet();
        $moduleSet->add($applicationBootstrap);

        $engine = new FcEngine();
        $engine->useContainer($application->container());
        $engine->boot($applicationBootstrap);

        $response = $engine->execute(
            $this->makeServerRequest('not-exists'),
            $moduleSet,
            $application
        );

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('', (string)$response->getBody());
    }

    /** @test */
    public function commandError(): void
    {
        $application = Application::instance();

        $applicationBootstrap = new class extends FcBootstrap {
            public function bootDependencies(Application $app): void
            {
                $app->addSingleton(Session::class, MemorySession::class);
                $app->addSingleton(HttpFactory::class, DiactorosHttpFactory::class);
            }

            public function bootDirectories(DirectorySet &$directorySet): void
            {
                $directorySet->add(new Directory(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
                    __DIR__ . "/Stubs/Commands"
                ));
            }
        };

        $applicationBootstrap->bootDependencies($application);

        $moduleSet = new ModuleSet();
        $moduleSet->add($applicationBootstrap);

        $engine = new FcEngine();
        $engine->useContainer($application->container());
        $engine->boot($applicationBootstrap);

        $response = $engine->execute(
            $this->makeServerRequest('three-command'),
            $moduleSet,
            $application
        );

        $file = '/application/tests/unit/AppEngine/'
              . 'FrontController/Stubs/Commands/ThreeCommand.php';

        $this->assertSame(500, $response->getStatusCode());
        $this->assertStringContainsString(
            "Error: Erro proposital on file {$file}[20]",
            (string)$response->getBody()
        );
    }

    private function makeServerRequest(string $uriPath): ServerRequestInterface
    {
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn($uriPath);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        return $request;
    }
}
