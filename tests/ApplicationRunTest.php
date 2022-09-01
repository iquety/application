<?php

declare(strict_types=1);

namespace Tests;

use ArrayObject;
use Freep\Application\Application;
use Freep\Application\Bootstrap;
use Freep\Application\Http\Request;
use Freep\Application\Http\Response;
use Freep\Application\Routing\Router;
use Laminas\Diactoros\Uri;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class ApplicationRunTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function runApplication(): void
    {

        $this->assertTrue(true);
        // $router->get('/comment/:id');
        // $router->put('/comment/:id')->policyBy(new class implements Policy {
        //     public function check(): bool { return false; }
        // });

        // $router->post('/comment/:id')->withController(fn() => (object)[]);

        // $router->delete('/comment/:id')->withController(fn() => (object)[]);

        // $app = TestCase::applicatioFactory();

        // /** @var Request $raquest */
        // $request = $app->make(Request::class);
        // $request->withUri(new Uri('http://www.teste.com.br/teste/33'));
        // $request->withMethod();

        // $app->run();
    }
}
