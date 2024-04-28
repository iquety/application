<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine;

use Closure;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\Bootstrap;
use OutOfBoundsException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tests\AppEngine\Support\EngineContainerAcessor;
use Tests\Unit\TestCase;

class AppEngineTest extends TestCase
{
    /** @test */
    public function dependencies(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage(
            'The container was not made available with the useContainer method'
        );

        $object = new EngineContainerAcessor();

        $object->invokeContainer();
    }
}
