<?php

declare(strict_types=1);

namespace Tests\Application\Support;

use Closure;
use Iquety\Application\AppEngine\MemoryEngine;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class NotFoundEngine extends MemoryEngine
{
    public function execute(
        RequestInterface $request,
        array $moduleList,
        Closure $bootModuleDependencies
    ): ?ResponseInterface
    {
        return null;
    }
}

