<?php

declare(strict_types=1);

namespace Tests\AppEngine\Mvc;

use ArrayObject;
use Iquety\Application\Application;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Routing\Route;
use Iquety\Routing\Router;
use stdClass;
use Tests\AppEngine\Mvc\Support\UserAlternateBootstrap;
use Tests\AppEngine\Mvc\Support\UserBootstrap;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ApplicationBootModuleTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function moduleInitialized(): void
    {
        // a fabrica cria a intancia de ServerRequestInterface com o
        // URI apondando para '/user/33'
        $app = TestCase::applicationFactory();

        $app->addEngine(MvcEngine::class);

        // O bootstrap do módulo cria uma rota apontando para o
        // padrão '/user/:id'
        $app->bootModule(new UserBootstrap());

        // as dependencias do módulo só serão resolvidas se
        // a rota '/user/:id' bater com a requisição efetuada para '/user/33'
        $app->run();

        $routeList = $app->make(Router::class)->routes();

        $this->assertCount(2, $routeList);

        $routeOne = $routeList[0];
        $routeTwo = $routeList[1];

        $this->assertInstanceOf(Route::class, $routeOne);
        $this->assertEquals(Route::GET, $routeOne->method());
        $this->assertEquals('user/:id', $routeOne->pattern());

        $this->assertInstanceOf(Route::class, $routeTwo);
        $this->assertEquals(Route::POST, $routeTwo->method());
        $this->assertEquals('user/:id', $routeTwo->pattern());

        // dependencias foram setadas após a resolução da rota
        $this->assertTrue($app->container()->has(ArrayObject::class));
        $this->assertTrue($app->container()->has(stdClass::class));
    }

    /** @test */
    public function moduleNotInitialized(): void
    {
        // a fabrica cria a intancia de ServerRequestInterface com o
        // URI apondando para '/user/33'
        $app = TestCase::applicationFactory();

        $app->addEngine(MvcEngine::class);

        // O bootstrap do módulo cria uma rota apontando para o
        // padrão '/editor/:id'
        $app->bootModule(new UserAlternateBootstrap());

        // as dependencias do módulo não serão resolvidas! Pois a
        // rota '/editor/:id' nunca vai bater com a requisição efetuada para '/user/33'
        $app->run();

        $routeList = $app->make(Router::class)->routes();

        $this->assertCount(2, $routeList);

        $routeOne = $routeList[0];
        $routeTwo = $routeList[1];

        $this->assertInstanceOf(Route::class, $routeOne);
        $this->assertEquals(Route::GET, $routeOne->method());
        $this->assertEquals('editor/:id', $routeOne->pattern());

        $this->assertInstanceOf(Route::class, $routeTwo);
        $this->assertEquals(Route::POST, $routeTwo->method());
        $this->assertEquals('editor/:id', $routeTwo->pattern());

        // as dependencias não foram setadas!!
        $this->assertFalse($app->container()->has(ArrayObject::class));
        $this->assertFalse($app->container()->has(stdClass::class));
    }
}
