<?php

declare(strict_types=1);

namespace Tests\Run;

use ArrayObject;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\Session\NativeSession;
use Iquety\Application\Application;
use Iquety\Application\Environment;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Application\Http\Session;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\EngineSet;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Iquety\Application\IoEngine\Mvc\MvcModule;
use Iquety\Application\RunWeb;
use Iquety\Injection\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RunWebDependenciesTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    public function tearDown(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function sessionDependency(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            'Please provide an implementation for the %s dependency ' .
            'in the given module in the Application->bootApplication or ' .
            'Application->bootModule method',
            'Session'
        ));

        $container = new Container();

        $this->makeFakeRequest($container);
    }

    /** @test */
    public function httpFactoryDependency(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            'Please provide an implementation for the %s dependency ' .
            'in the given module in the Application->bootApplication or ' .
            'Application->bootModule method',
            'HttpFactory'
        ));

        $container = new Container();
        $container->addFactory(Session::class, new NativeSession());

        $this->makeFakeRequest($container);
    }

    /** @test */
    public function httpDependencies(): void
    {
        $container = new Container();

        // disponibiliza as dependências obrigatórias
        $container->addFactory(Session::class, new NativeSession());
        $container->addFactory(HttpFactory::class, new DiactorosHttpFactory());

        // certifica que o container não possui as dependencias Http
        $this->assertFalse($container->has(StreamInterface::class));
        $this->assertFalse($container->has(UriInterface::class));
        $this->assertFalse($container->has(ResponseInterface::class));
        $this->assertFalse($container->has(HttpResponseFactory::class));
        $this->assertFalse($container->has(ServerRequestInterface::class));
        $this->assertFalse($container->has(Input::class));
        $this->assertFalse($container->has(Application::class));

        /** @var DiactorosHttpFactory $factory */
        $factory = $container->get(HttpFactory::class);

        $originalRequest = $factory->createRequestFromGlobals();
        $this->assertSame('', $originalRequest->getHeaderLine('Accept'));
        $this->assertSame('', $originalRequest->getHeaderLine('Environment'));

        // executa o motor Web
        $this->makeRunnner($container)->run($originalRequest);

        // certifica que a execução gerou as dependências Http
        $this->assertTrue($container->has(StreamInterface::class));
        $this->assertTrue($container->has(UriInterface::class));
        $this->assertTrue($container->has(ResponseInterface::class));
        $this->assertTrue($container->has(HttpResponseFactory::class));
        $this->assertTrue($container->has(ServerRequestInterface::class));
        $this->assertTrue($container->has(Input::class));
        $this->assertTrue($container->has(Application::class));

        $request = $container->get(ServerRequestInterface::class);

        $this->assertSame('text/html', $request->getHeaderLine('Accept'));
        $this->assertSame(
            Environment::DEVELOPMENT->value,
            $request->getHeaderLine('Environment')
        );

        /** @var ResponseInterface $response */
        $response = $container->getWithArguments(ResponseInterface::class, [201, 'Test']);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('Test', (string)$response->getReasonPhrase());
    }

    /** @test */
    public function invalidHttpFactoryDependency(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'The implementation provided to the HttpFactory dependency in the module ' .
            'provided in the Application->bootApplication method is invalid',
        );

        $container = new Container();

        // disponibiliza as dependências obrigatórias
        $container->addFactory(Session::class, new NativeSession());

        // dependência inválida para HttpFactory
        $container->addFactory(HttpFactory::class, new ArrayObject());

        /** @var ServerRequestInterface $request */
        $request = $this->createMock(ServerRequestInterface::class);

        // executa o motor Web
        $this->makeRunnner($container)->run($request);
    }

    /** @test */
    public function invalidSessionDependency(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'The implementation provided to the Session dependency in the module ' .
            'provided in the Application->bootApplication method is invalid',
        );

        $container = new Container();

        // disponibiliza as dependências obrigatórias
        $container->addFactory(HttpFactory::class, new DiactorosHttpFactory());

        // dependência inválida para Session
        $container->addFactory(Session::class, new ArrayObject());

        /** @var DiactorosHttpFactory $factory */
        $factory = $container->get(HttpFactory::class);

        // executa o motor Web
        $originalRequest = $factory->createRequestFromGlobals();

        // executa o motor Web
        $this->makeRunnner($container)->run($originalRequest);
    }

    private function makeRunnner(Container $container): RunWeb
    {
        $engineSet = new EngineSet($container);

        $engineSet->add(new MvcEngine());

        $module = new class extends MvcModule
        {
            public function bootDependencies(Container $container): void
            {
                // ...
            }
        };

        $runner = new RunWeb(
            Environment::DEVELOPMENT,
            $container,
            $module,
            $engineSet
        );

        return $runner;
    }

    private function makeFakeRequest(Container $container): ResponseInterface
    {
        $runner = $this->makeRunnner($container);

        /** @var ServerRequestInterface $fakeRequest */
        $fakeRequest = $this->createMock(ServerRequestInterface::class);

        return $runner->run($fakeRequest);
    }
}
