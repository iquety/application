<?php

declare(strict_types=1);

namespace Tests\AppEngine;

use Closure;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\Bootstrap;
use OutOfBoundsException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

class AppEngineTest extends TestCase
{
    /** @test */
    public function dependencies(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage(
            'The container was not made available with the useContainer method'
        );

        $object = new class extends AppEngine
        {
            public function boot(Bootstrap $bootstrap): void
            {}

            public function invokeContainer(): void
            {
                $this->container();
            }

            public function execute(
                RequestInterface $request,
                array $moduleList,
                Closure $bootDependencies
            ): ?ResponseInterface {
                return null;
            }
        };

        $object->invokeContainer();
    }
}
