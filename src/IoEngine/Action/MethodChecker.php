<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Action;

use Iquety\Http\HttpMethod;
use Psr\Http\Message\ServerRequestInterface;

trait MethodChecker
{
    public function forMethod(HttpMethod $method = HttpMethod::ANY): void
    {
        /** @var ServerRequestInterface */
        $request = $this->make(ServerRequestInterface::class);

        $requestMethod = $request->getMethod();

        if ($method === HttpMethod::ANY || $method->value === $requestMethod) {
            return;
        }

        throw new MethodNotAllowedException('');
    }
}
