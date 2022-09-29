<?php

declare(strict_types=1);

namespace Tests\Support;

use Freep\Application\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class UserController
{
    public function __construct(private Application $app)
    {
    }

    public function create(ServerRequestInterface $request, int $id): ResponseInterface
    {
        return $this->app->make(ResponseInterface::class)->withBody(
            $this->app->make(StreamInterface::class, $request->getUri()->getPath() . ' - ID: ' . $id)
        );
    }
}
