<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use Psr\Http\Message\ResponseInterface;

abstract class Command
{
    public const METHOD_GET = 'GET';

    public const METHOD_POST = 'POST';

    public const METHOD_ANY = 'ANY';

    private string $method = self::METHOD_GET;

    public function method(): string
    {
        return $this->method;
    }

    public function useMethod(string $method): void
    {
        $this->method = $method;
    }

    abstract public function execute(): ResponseInterface;
}
