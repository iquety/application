<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\Mvc;

use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\Action\MethodNotAllowedException;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpMethod;
use Psr\Http\Message\ServerRequestInterface;
use Tests\Unit\AppEngine\Mvc\Stubs\AnyController;
use Tests\Unit\AppEngine\Mvc\Stubs\CheckMethodController;
use Tests\Unit\AppEngine\Mvc\Stubs\DeleteController;
use Tests\Unit\AppEngine\Mvc\Stubs\GetController;
use Tests\Unit\AppEngine\Mvc\Stubs\PatchController;
use Tests\Unit\AppEngine\Mvc\Stubs\PostController;
use Tests\Unit\AppEngine\Mvc\Stubs\PutController;
use Tests\Unit\TestCase;

class ControllerHttpMethodTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    public function httpMethodProvider(): array
    {
        $list = [];

        $list['check get'] = [ new GetController(), HttpMethod::GET->value ];
        $list['check post'] = [ new PostController(), HttpMethod::POST->value ];
        $list['check put'] = [ new PutController(), HttpMethod::PUT->value ];
        $list['check patch'] = [ new PatchController(), HttpMethod::PATCH->value ];
        $list['check delete'] = [ new DeleteController(), HttpMethod::DELETE->value ];

        return $list;
    }

    /**
     * @test
     * @dataProvider httpMethodProvider
     */
    public function forMethod(CheckMethodController $controller, string $httpMethod): void
    {
        $application = Application::instance();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getMethod')
            ->willReturn($httpMethod);

        $application->container()->addSingleton(ServerRequestInterface::class, $request);

        $this->assertSame(
            "Resposta $httpMethod para id 22 input 0=name",
            $controller->execute(Input::fromString(mb_strtolower($httpMethod) . '/name'), 22)
        );
    }

    public function anyHttpMethodProvider(): array
    {
        $list = [];

        $list['check get == any'] = [ new AnyController(), HttpMethod::GET->value, 'ANY' ];
        $list['check post == any'] = [ new AnyController(), HttpMethod::POST->value, 'ANY' ];
        $list['check put == any'] = [ new AnyController(), HttpMethod::PUT->value, 'ANY' ];
        $list['check patch == any'] = [ new AnyController(), HttpMethod::PATCH->value, 'ANY' ];
        $list['check delete == any'] = [ new AnyController(), HttpMethod::DELETE->value, 'ANY' ];

        return $list;
    }

    /**
     * @test
     * @dataProvider anyHttpMethodProvider
     */
    public function forAnyMethod(CheckMethodController $controller, string $httpMethod): void
    {
        $application = Application::instance();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getMethod')
            ->willReturn($httpMethod);

        $application->container()->addSingleton(ServerRequestInterface::class, $request);

        $this->assertSame(
            "Resposta ANY para id 22 input 0=name",
            $controller->execute(Input::fromString(mb_strtolower($httpMethod) . '/name'), 22)
        );
    }

    public function invalidHttpMethodProvider(): array
    {
        $list = [];

        $list['check get != post'] = [ new GetController(), HttpMethod::POST->value ];
        $list['check get != put'] = [ new GetController(), HttpMethod::PUT->value ];
        $list['check get != patch'] = [ new GetController(), HttpMethod::PATCH->value ];
        $list['check get != delete'] = [ new GetController(), HttpMethod::DELETE->value ];

        $list['check post != get'] = [ new PostController(), HttpMethod::GET->value ];
        $list['check post != put'] = [ new PostController(), HttpMethod::PUT->value ];
        $list['check post != patch'] = [ new PostController(), HttpMethod::PATCH->value ];
        $list['check post != delete'] = [ new PostController(), HttpMethod::DELETE->value ];

        $list['check put != get'] = [ new PutController(), HttpMethod::GET->value ];
        $list['check put != post'] = [ new PutController(), HttpMethod::POST->value ];
        $list['check put != patch'] = [ new PutController(), HttpMethod::PATCH->value ];
        $list['check put != delete'] = [ new PutController(), HttpMethod::DELETE->value ];

        $list['check patch != get'] = [ new PatchController(), HttpMethod::GET->value ];
        $list['check patch != post'] = [ new PatchController(), HttpMethod::POST->value ];
        $list['check patch != put'] = [ new PatchController(), HttpMethod::PUT->value ];
        $list['check patch != delete'] = [ new PatchController(), HttpMethod::DELETE->value ];

        $list['check delete != get'] = [ new DeleteController(), HttpMethod::GET->value ];
        $list['check delete != post'] = [ new DeleteController(), HttpMethod::POST->value ];
        $list['check delete != put'] = [ new DeleteController(), HttpMethod::PUT->value ];
        $list['check delete != patch'] = [ new DeleteController(), HttpMethod::PATCH->value ];

        return $list;
    }
    
    /**
     * @test
     * @dataProvider invalidHttpMethodProvider
     */
    public function forMethodException(CheckMethodController $controller, string $httpMethod): void
    {
        $this->expectException(MethodNotAllowedException::class);
        $this->expectExceptionMessage('');

        $application = Application::instance();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getMethod')
            ->willReturn($httpMethod);

        $application->container()->addSingleton(ServerRequestInterface::class, $request);

        $controller->execute(Input::fromString(mb_strtolower($httpMethod) . '/name'), 22);
    }
}
