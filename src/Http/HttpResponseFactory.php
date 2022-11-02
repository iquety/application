<?php

declare(strict_types=1);

namespace Iquety\Application\Http;

use Psr\Http\Message\ResponseInterface;
use Throwable;

class HttpResponseFactory
{
    public function __construct(private HttpFactory $factory)
    {
    }

    public function response(
        string $content = '',
        int $status = HttpStatus::HTTP_OK,
        string $mimeType = ''
    ): ResponseInterface {
        return $this->rawResponse($content, $status, $mimeType);
    }

    /** @param array<mixed,mixed> $content */
    public function jsonResponse(array $content = [], int $status = HttpStatus::HTTP_OK): ResponseInterface
    {
        $jsonContent = (string)json_encode($content, JSON_FORCE_OBJECT);

        return $this->response($jsonContent, $status, 'application/json');
    }

    public function notFoundResponse(string $content = ''): ResponseInterface
    {
        return $this->response($content, HttpStatus::HTTP_NOT_FOUND);
    }

    public function accessDeniedResponse(string $content = ''): ResponseInterface
    {
        return $this->response($content, HttpStatus::HTTP_FORBIDDEN);
    }

    public function serverErrorResponse(Throwable $exception): ResponseInterface
    {
        return $this->response($exception->getMessage(), HttpStatus::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function rawResponse(
        string $content = '',
        int $status = HttpStatus::HTTP_OK,
        string $mimeType = ''
    ): ResponseInterface {
        $response = $this->factory->createResponse($status, HttpStatus::reason($status));

        if ($content == '') {
            return $response;
        }

        $stream = $this->factory->createStream($content);
        $stream->rewind();

        $response = $response->withBody($stream);
        
        if ($mimeType !== '') {
            return $response->withAddedHeader('Content-type', $mimeType);
        }

        return $response;
    }
}
