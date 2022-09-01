<?php

declare(strict_types=1);

namespace Freep\Application\Http;

use Exception;
use Freep\Application\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;
use Throwable;

class ResponseFactory
{
    public function __construct(private Application $app)
    {}

    public function notFoundResponse(string $content = ''): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = $this->app->make(ResponseInterface::class, 404);

        if ($content !== '') {
            return $response->withBody(
                $this->app->make(StreamInterface::class, $content)
            );
        }

        return $response;
    }

    public function accessDeniedResponse(string $content = ''): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = $this->app->make(ResponseInterface::class, 403);

        if ($content !== '') {
            return $response->withBody(
                $this->app->make(StreamInterface::class, $content)
            );
        }

        return $response;
    }

    public function serverErrorResponse(Throwable $exception): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = $this->app->make(ResponseInterface::class, 404);
        
        return $response->withBody(
            $this->app->make(StreamInterface::class, $exception->getMessage())
        );
    }
}
