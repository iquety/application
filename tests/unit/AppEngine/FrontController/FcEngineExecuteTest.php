<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\Directory;
use Iquety\Application\AppEngine\FrontController\DirectorySet;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use Tests\Unit\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class FcEngineExecuteTest extends TestCase
{
    /** @test */
    public function executeWithoutDirectories(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No directories registered as command source');

        /** @var RequestInterface */
        $request = $this->createMock(RequestInterface::class);

        $engine = new FcEngine();

        $engine->useContainer(new Container());

        $engine->boot(new class extends FcBootstrap {
            public function bootDependencies(Application $app): void {}
        });

        $engine->execute($request, [], function(){});
    }

    /** @test */
    public function executeDirectories(): void
    {
        // $this->expectException(RuntimeException::class);
        // $this->expectExceptionMessage('No directories registered as command source');

        /** @var RequestInterface */
        $request = $this->createMock(RequestInterface::class);

        $engine = new FcEngine();

        $engine->useContainer(new Container());

        $engine->boot(new class extends FcBootstrap {
            public function bootDependencies(Application $app): void {}

            public function bootDirectories(DirectorySet $directories): void
            {
                $directories->add(new Directory(
                    'Tests\Unit\AppEngine\FrontController\Stubs',
                    __DIR__ . "/Stubs"
                ));
            }
        });

        $engine->execute($request, [], function(){});
    }
}
