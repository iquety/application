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
class FcEngineBootTest extends TestCase
{
    /** @return array<string,array<int,Bootstrap>> */
    public function invalidEngineProvider(): array
    {
        $list = [];

        /** @var Bootstrap */
        $bootstrap = $this->createMock(Bootstrap::class);

        $mvcBootstrap = new class extends MvcBootstrap {
            public function bootRoutes(Router $router): void {} 
    
            public function bootDependencies(Application $app): void {}
        };


        $list['mock of not FcBootstrap'] = [ $bootstrap ];

        $list['mvc is not FcBootstrap'] = [ $mvcBootstrap ];

        return $list;
    }

    /**
     * @test
     * @dataProvider invalidEngineProvider
     */
    public function bootInvalidEngine(Bootstrap $bootstrap): void
    {
        $engine = new FcEngine();

        $engine->boot($bootstrap);

        $this->assertTrue(true);
    }

    /** @test */
    public function bootWithoutContainer(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The container was not made available with the useContainer method');

        $engine = new FcEngine();

        $engine->boot(new class extends FcBootstrap {
            public function bootDependencies(Application $app): void {}
        });
    }

    /** @test */
    public function bootWithoutDirectories(): void
    {
        $engine = new FcEngine();

        $engine->useContainer(new Container());

        $engine->boot(new class extends FcBootstrap {
            public function bootDependencies(Application $app): void {}
        });

        $directorySet = $engine->getDirectorySet();

        $this->assertInstanceOf(DirectorySet::class, $directorySet);
        $this->assertCount(0, $directorySet->toArray());
    }

    /** @test */
    public function bootDirectories(): void
    {
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

        $directorySet = $engine->getDirectorySet();

        $this->assertInstanceOf(DirectorySet::class, $directorySet);
        $this->assertCount(1, $directorySet->toArray());
    }
}
