<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

use Iquety\Application\Http\HttpMethod;
use Psr\Http\Message\ServerRequestInterface;

trait MethodChecker
{
    public function forMethod(string $method = HttpMethod::ANY): void
    {
        /** @var ServerRequestInterface */
        $request = $this->make(ServerRequestInterface::class);

        $requestMethod = $request->getMethod();

        if ($method === HttpMethod::ANY || $method === $requestMethod) {
            return;
        }

        throw new MethodNotAllowedException('');
    }
}
