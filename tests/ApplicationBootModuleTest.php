<?php

declare(strict_types=1);

namespace Tests;

use ArrayObject;
use Freep\Application\Application;
use Freep\Application\Http\Request;
use Freep\Application\Routing\Route;
use Freep\Application\Routing\Router;
use stdClass;

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

        // O bootstrap do módulo cria uma rota apontando para o
        // padrão '/user/:id'
        $app->bootModule(new UserModuleBootstrap());

        // as dependencias do módulo só serão resolvidas se 
        // a rota '/user/:id' bater com a requisição efetuada para '/user/33'
        $app->run();

        $routeList = $app->router()->routes();

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

        // O bootstrap do módulo cria uma rota apontando para o
        // padrão '/post/:id'
        $app->bootModule(new PostModuleBootstrap());

        // as dependencias do módulo não serão resolvidas! Pois a
        // rota '/post/:id' nunca vai bater com a requisição efetuada para '/user/33'
        $app->run();

        $routeList = $app->router()->routes();

        $this->assertCount(2, $routeList);

        $routeOne = $routeList[0];
        $routeTwo = $routeList[1];

        $this->assertInstanceOf(Route::class, $routeOne);
        $this->assertEquals(Route::GET, $routeOne->method());
        $this->assertEquals('post/:id', $routeOne->pattern());

        $this->assertInstanceOf(Route::class, $routeTwo);
        $this->assertEquals(Route::POST, $routeTwo->method());
        $this->assertEquals('post/:id', $routeTwo->pattern());

        // as dependencias não foram setadas!!
        $this->assertFalse($app->container()->has(ArrayObject::class));
        $this->assertFalse($app->container()->has(stdClass::class));
    }
}