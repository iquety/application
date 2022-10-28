<?php

declare(strict_types=1);

namespace Tests\Routing;

use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\Http\Session;
use Iquety\Application\Routing\Route;
use Iquety\Application\Routing\Router;
use Iquety\Injection\Container;
use Tests\Support\IocPolicy;
use Tests\TestCase;

class RouterIocTest extends TestCase
{
    /** @test */
    public function iocSessionDenied(): void
    {
        $container = new Container();
        $container->registerSingletonDependency(Session::class, MemorySession::class);

        $router = new Router();
        $router->useContainer($container);
        $router->get('edit/:id')->policyBy(IocPolicy::class);

        $this->assertFalse($router->routeNotFound());
        $this->assertFalse($router->routeDenied());
        $this->assertNull($router->currentRoute());

        $router->process(Route::GET, "edit/33");

        $this->assertFalse($router->routeNotFound());
        $this->assertTrue($router->routeDenied());
        $this->assertInstanceOf(Route::class, $router->currentRoute());
    }

    /** @test */
    public function iocSessionAllowed(): void
    {
        $container = new Container();
        $container->registerSingletonDependency(Session::class, MemorySession::class);
        $container->get(Session::class)->setParam('allow', 'yes');

        $router = new Router();
        $router->useContainer($container);
        $router->get('edit/:id')->policyBy(IocPolicy::class);

        $this->assertFalse($router->routeNotFound());
        $this->assertFalse($router->routeDenied());
        $this->assertNull($router->currentRoute());

        $router->process(Route::GET, "edit/33");

        $this->assertFalse($router->routeNotFound());
        $this->assertFalse($router->routeDenied());
        $this->assertInstanceOf(Route::class, $router->currentRoute());
    }

    /** @test */
    public function iocContainerReference(): void
    {
        $container = new Container();
        $container->registerSingletonDependency(Session::class, MemorySession::class);

        $router = new Router();
        $router->useContainer($container);
        $router->get('edit/:id')->policyBy(IocPolicy::class);

        // aplica o valor após o uso da variável $container
        $container->get(Session::class)->setParam('allow', 'yes');

        $this->assertFalse($router->routeNotFound());
        $this->assertFalse($router->routeDenied());
        $this->assertNull($router->currentRoute());

        $router->process(Route::GET, "edit/33");

        $this->assertFalse($router->routeNotFound());
        $this->assertFalse($router->routeDenied());
        $this->assertInstanceOf(Route::class, $router->currentRoute());
    }
}
