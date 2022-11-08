<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController\Support\Commands;

use Iquety\Application\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class NoContractCommand
{
    public function __construct(private Application $app)
    {
    }

    /** @SuppressWarnings(PHPMD.ShortVariable) */
    public function create(ServerRequestInterface $request, int $identity): ResponseInterface
    {
        return $this->app->make(ResponseInterface::class)->withBody(
            $this->app->make(
                StreamInterface::class,
                $request->getUri()->getPath() . ' - ID: ' . $identity
            )
        );
    }
}
