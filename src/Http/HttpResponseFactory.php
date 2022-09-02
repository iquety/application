<?php

declare(strict_types=1);

namespace Freep\Application\Http;

use Freep\Application\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Throwable;

class HttpResponseFactory
{
    public function __construct(private Application $app)
    {}

    private function addHeader(ResponseInterface $response, string $name, string $value): ResponseInterface
    {
        if ($value == '') {
            return $response;
        }

        return $response->withAddedHeader($name, $value);
    }

    private function setBody(ResponseInterface $response, string $content): ResponseInterface
    {
        if ($content == '') {
            return $response;
        }

        $stream = $this->app->make(StreamInterface::class, $content);

        return $response->withBody($stream);
    }

    private function setMimeType(ResponseInterface $response, string $mimeType): ResponseInterface
    {
        return $this->addHeader($response, 'Content-type', $mimeType);
    }

    public function response(string $content = '', int $status = 200, string $mimeType = null): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = $this->app->make(ResponseInterface::class, $status, 'OK');

        $response = $this->setBody($response, $content);
        $response = $this->setMimeType($response, $mimeType);

        return $response;
    }

    public function notFoundResponse(string $content = ''): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = $this->app->make(ResponseInterface::class, 404, 'Not Found');

        return $this->setBody($response, $content);
    }

    public function accessDeniedResponse(string $content = ''): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = $this->app->make(ResponseInterface::class, 403, 'Forbidden');

        return $this->setBody($response, $content);
    }

    public function serverErrorResponse(Throwable $exception): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = $this->app->make(ResponseInterface::class, 500, 'Internal Server Error');
        
        return $this->setBody($response, $exception->getMessage());
    }
}
