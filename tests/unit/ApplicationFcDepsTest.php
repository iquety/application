<?php

declare(strict_types=1);

namespace Tests\Unit;

use Iquety\Application\AppEngine\Bootstrap;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpMethod;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class ApplicationFcDepsTest extends ApplicationCase
{
    /** @test */
    public function runWithoutDependecies(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Please provide an implementation for the dependency Session ' .
            'in the bootstrap provided in the Application->bootApplication method'
        );

        $instance = Application::instance();

        $instance->container()->addSingleton(
            ServerRequestInterface::class,
            $this->makeServerRequest('sub-directory/two-command', HttpMethod::ANY)
        );

        $instance->bootEngine(new FcEngine());

        $instance->bootApplication($this->makeFcBootstrapWithoutDependencies());

        $instance->run();
    }

    /** @test */
    public function runInvalidSession(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'The implementation provided to the Session dependency ' .
            'in the bootstrap provided in the Application->bootApplication method is invalid'
        );

        $instance = Application::instance();

        $instance->container()->addSingleton(
            ServerRequestInterface::class,
            $this->makeServerRequest('sub-directory/two-command', HttpMethod::ANY)
        );

        $instance->bootEngine(new FcEngine());

        $instance->bootApplication($this->makeFcBootstrapInvalidSession());

        $instance->run();
    }

    /** @test */
    public function runSessionWithoutHttpFactory(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Please provide an implementation for the dependency ' .
            'HttpFactory in the bootstrap provided in the Application->bootApplication method'
        );

        $instance = Application::instance();

        $instance->container()->addSingleton(
            ServerRequestInterface::class,
            $this->makeServerRequest('sub-directory/two-command', HttpMethod::ANY)
        );

        $instance->bootEngine(new FcEngine());

        $instance->bootApplication($this->makeFcBootstrapSession());

        $instance->run();
    }

    /** @test */
    public function runSessionInvalidHttpFactory(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'The implementation provided to the HttpFactory ' .
            'dependency in the bootstrap provided in the Application->bootApplication method is invalid'
        );

        $instance = Application::instance();

        $instance->container()->addSingleton(
            ServerRequestInterface::class,
            $this->makeServerRequest('sub-directory/two-command', HttpMethod::ANY)
        );

        $instance->bootEngine(new FcEngine());

        $instance->bootApplication($this->makeFcBootstrapSessionInvalidHttpFactory());

        $instance->run();
    }

    public function bootstrapHttpFactoryProvider(): array
    {
        $list = [];

        $list['Diactoros'] = [ $this->makeFcBootstrapSessionDiactoros() ];
        $list['Guzzle']    = [ $this->makeFcBootstrapSessionGuzzle() ];
        $list['NyHolm']    = [ $this->makeFcBootstrapSessionNyHolm() ];

        return $list;
    }

    /**
     * @test
     * @dataProvider bootstrapHttpFactoryProvider
     */
    public function runSessionHttpFactory(Bootstrap $bootstrap): void
    {
        $instance = Application::instance();

        $instance->container()->addSingleton(
            ServerRequestInterface::class,
            $this->makeServerRequest('sub-directory/two-command', HttpMethod::ANY)
        );

        $instance->bootEngine(new FcEngine());

        $instance->bootApplication($bootstrap);

        $response = $instance->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
