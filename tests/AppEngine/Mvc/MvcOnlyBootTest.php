<?php

declare(strict_types=1);

namespace Tests\AppEngine\Mvc;

use ArrayObject;
use Exception;
use InvalidArgumentException;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Application\Bootstrap;
use Iquety\Application\Http\HttpStatus;
use Iquety\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

class MvcOnlyBootTest extends TestCase
{
    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function bootException(string $httpFactoryContract): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('Invalid bootstrap. Required a %s', MvcBootstrap::class)
        );

        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, MvcEngine::class);

        /** @var Bootstrap $bootstrap */
        $bootstrap = $this->createMock(Bootstrap::class);

        $engine->boot($bootstrap);
    }
}
