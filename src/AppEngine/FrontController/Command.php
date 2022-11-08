<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

abstract class Command
{
    public const METHOD_GET = 'GET';

    public const METHOD_POST = 'POST';

    public const METHOD_PUT = 'PUT';

    public const METHOD_PATCH = 'PATCH';

    public const METHOD_DELETE = 'DELETE';

    public const METHOD_ANY = 'ANY';

    private string $method = self::METHOD_ANY;

    public function method(): string
    {
        return $this->method;
    }

    public function onlyMethod(string $method): void
    {
        $this->method = $method;
    }
}
