<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

use Closure;
use Iquety\Application\Bootstrap;
use InvalidArgumentException;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Injection\InversionOfControl;
use Iquety\Routing\Router;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class MemoryEngine extends AppEngine
{
    public function boot(Bootstrap $bootstrap): void
    {
    }

    public function execute(
        RequestInterface $request,
        array $moduleList,
        Closure $bootModuleDependencies
    ): ?ResponseInterface
    {
        return $this->responseFactory()->response('');
    }
}
