<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController;

use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\AppEngine\FrontController\CommandDescriptor;
use Iquety\Application\AppEngine\FrontController\CommandHandler;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use Tests\AppEngine\FrontController\Support\CommandsDir\OneTwoThree;
use Tests\AppEngine\FrontController\Support\FcBootstrapConcrete;
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


        $list['mock'] = [ $bootstrap ];

        $list['mvc'] = [ $mvcBootstrap ];

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

        
    }

    // /** @test */
    // public function bootWithoutDirectories(): void
    // {
    //     $this->expectException(RuntimeException::class);
    //     $this->expectExceptionMessage('No directories registered as command source');

    //     /** @var RequestInterface */
    //     $request = $this->createMock(RequestInterface::class);

    //     $engine = new FcEngine();

    //     $engine->useContainer(new Container());

    //     $engine->boot(new class extends FcBootstrap {
    //         public function bootDependencies(Application $app): void {}
    //     });

    //     $engine->execute($request, [], function(){});
    // }
}
