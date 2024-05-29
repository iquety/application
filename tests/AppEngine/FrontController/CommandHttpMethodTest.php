<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController;

use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\Action\MethodNotAllowedException;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpMethod;
use Psr\Http\Message\ServerRequestInterface;
use Tests\AppEngine\FrontController\Stubs\Commands\AnyCommand;
use Tests\AppEngine\FrontController\Stubs\Commands\CheckMethodCommand;
use Tests\AppEngine\FrontController\Stubs\Commands\DeleteCommand;
use Tests\AppEngine\FrontController\Stubs\Commands\GetCommand;
use Tests\AppEngine\FrontController\Stubs\Commands\PatchCommand;
use Tests\AppEngine\FrontController\Stubs\Commands\PostCommand;
use Tests\AppEngine\FrontController\Stubs\Commands\PutCommand;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class CommandHttpMethodTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @return array<string,array<int,mixed>> */
    public function httpMethodProvider(): array
    {
        $list = [];

        $list['check get'] = [ new GetCommand(), HttpMethod::GET->value ];
        $list['check post'] = [ new PostCommand(), HttpMethod::POST->value ];
        $list['check put'] = [ new PutCommand(), HttpMethod::PUT->value ];
        $list['check patch'] = [ new PatchCommand(), HttpMethod::PATCH->value ];
        $list['check delete'] = [ new DeleteCommand(), HttpMethod::DELETE->value ];

        return $list;
    }

    /**
     * @test
     * @dataProvider httpMethodProvider
     */
    public function forMethod(CheckMethodCommand $command, string $httpMethod): void
    {
        $application = Application::instance();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getMethod')
            ->willReturn($httpMethod);

        $application->container()->addSingleton(ServerRequestInterface::class, $request);

        $this->assertSame(
            "Resposta $httpMethod para id 22 input 0=name",
            $command->execute(Input::fromString(mb_strtolower($httpMethod) . '/name'), 22)
        );
    }

    /** @return array<string,array<int,mixed>> */
    public function anyHttpMethodProvider(): array
    {
        $list = [];

        $list['check get == any'] = [ new AnyCommand(), HttpMethod::GET->value, 'ANY' ];
        $list['check post == any'] = [ new AnyCommand(), HttpMethod::POST->value, 'ANY' ];
        $list['check put == any'] = [ new AnyCommand(), HttpMethod::PUT->value, 'ANY' ];
        $list['check patch == any'] = [ new AnyCommand(), HttpMethod::PATCH->value, 'ANY' ];
        $list['check delete == any'] = [ new AnyCommand(), HttpMethod::DELETE->value, 'ANY' ];

        return $list;
    }

    /**
     * @test
     * @dataProvider anyHttpMethodProvider
     */
    public function forAnyMethod(CheckMethodCommand $command, string $httpMethod): void
    {
        $application = Application::instance();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getMethod')
            ->willReturn($httpMethod);

        $application->container()->addSingleton(ServerRequestInterface::class, $request);

        $this->assertSame(
            "Resposta ANY para id 22 input 0=name",
            $command->execute(Input::fromString(mb_strtolower($httpMethod) . '/name'), 22)
        );
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidHttpMethodProvider(): array
    {
        $list = [];

        $list['check get != post'] = [ new GetCommand(), HttpMethod::POST->value ];
        $list['check get != put'] = [ new GetCommand(), HttpMethod::PUT->value ];
        $list['check get != patch'] = [ new GetCommand(), HttpMethod::PATCH->value ];
        $list['check get != delete'] = [ new GetCommand(), HttpMethod::DELETE->value ];

        $list['check post != get'] = [ new PostCommand(), HttpMethod::GET->value ];
        $list['check post != put'] = [ new PostCommand(), HttpMethod::PUT->value ];
        $list['check post != patch'] = [ new PostCommand(), HttpMethod::PATCH->value ];
        $list['check post != delete'] = [ new PostCommand(), HttpMethod::DELETE->value ];

        $list['check put != get'] = [ new PutCommand(), HttpMethod::GET->value ];
        $list['check put != post'] = [ new PutCommand(), HttpMethod::POST->value ];
        $list['check put != patch'] = [ new PutCommand(), HttpMethod::PATCH->value ];
        $list['check put != delete'] = [ new PutCommand(), HttpMethod::DELETE->value ];

        $list['check patch != get'] = [ new PatchCommand(), HttpMethod::GET->value ];
        $list['check patch != post'] = [ new PatchCommand(), HttpMethod::POST->value ];
        $list['check patch != put'] = [ new PatchCommand(), HttpMethod::PUT->value ];
        $list['check patch != delete'] = [ new PatchCommand(), HttpMethod::DELETE->value ];

        $list['check delete != get'] = [ new DeleteCommand(), HttpMethod::GET->value ];
        $list['check delete != post'] = [ new DeleteCommand(), HttpMethod::POST->value ];
        $list['check delete != put'] = [ new DeleteCommand(), HttpMethod::PUT->value ];
        $list['check delete != patch'] = [ new DeleteCommand(), HttpMethod::PATCH->value ];

        return $list;
    }

    /**
     * @test
     * @dataProvider invalidHttpMethodProvider
     */
    public function forMethodException(CheckMethodCommand $command, string $httpMethod): void
    {
        $this->expectException(MethodNotAllowedException::class);
        $this->expectExceptionMessage('');

        $application = Application::instance();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getMethod')
            ->willReturn($httpMethod);

        $application->container()->addSingleton(ServerRequestInterface::class, $request);

        $command->execute(Input::fromString(mb_strtolower($httpMethod) . '/name'), 22);
    }
}
