<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\Support;

use Closure;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\Bootstrap;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
class EngineContainerAcessor extends AppEngine
{
    public function boot(Bootstrap $bootstrap): void
    {
    }

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
}
