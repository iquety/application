<?php

declare(strict_types=1);

namespace Tests\AppEngine\Mvc;

use ArrayObject;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Routing\Router;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

class MvcOnlyResolveClosureTest extends TestCase
{
    /** @return array<string,array<mixed>> */
    public function returnTypesProvider(): array
    {
        $returnTypes = [
            'response' => [ $this->createMock(ResponseInterface::class) ],
            'string'   => [ 'Retorno do closure em forma de string' ],
            'integer'  => [ 1234567 ],
            'array'    => [ ['teste' => 'unidade'] ],
            'object'   => [ (object)['teste' => 'unidade'] ],
            'jsonable' => [ new ArrayObject(['teste' => 'unidade']) ],
        ];

        $httpFactories = $this->httpFactoryProvider();

        $list = [];

        foreach ($returnTypes as $index => $returnValue) {
            foreach ($httpFactories as $name => $contractValue) {
                $label = $name . ' ' . $index;

                $list[$label] = [$contractValue[0], $returnValue[0]];
            }
        }

        return $list;
    }

    /**
     * @test
     * @dataProvider returnTypesProvider
     */
    public function executeResolveClosure(string $httpFactoryContract, mixed $returnType): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, MvcEngine::class);
        $request = $this->requestFactory($httpFactory, 'user/42');

        $callback = function (Router $router) use ($returnType) {
            $router->get('/user/:id')
                ->usingAction(fn() => $returnType);
        };

        /** @var InvocationMocker */
        $bootstrap = $this->createMock(MvcBootstrap::class);
        $bootstrap->method('bootRoutes')->will($this->returnCallback($callback));

        /** @var MvcBootstrap $bootstrap */
        $engine->boot($bootstrap);

        $response = $engine->execute(
            $request,
            [$bootstrap::class => &$bootstrap],
            fn() => null
        );

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function executeResolveClosureNull(string $httpFactoryContract): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, MvcEngine::class);
        $request = $this->requestFactory($httpFactory, 'user/42');

        $callback = function (Router $router) {
            $router->get('/user/:id')
                ->usingAction(fn() => null);
        };

        /** @var InvocationMocker */
        $bootstrap = $this->createMock(MvcBootstrap::class);
        $bootstrap->method('bootRoutes')->will($this->returnCallback($callback));

        /** @var MvcBootstrap $bootstrap */
        $engine->boot($bootstrap);

        $response = $engine->execute(
            $request,
            [$bootstrap::class => &$bootstrap],
            fn() => null
        );

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('', (string)$response?->getBody());
    }
}
