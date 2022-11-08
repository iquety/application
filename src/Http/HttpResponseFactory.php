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
        int $status = HttpStatus::OK,
        string $mimeType = ''
    ): ResponseInterface {
        return $this->rawResponse($content, $status, $mimeType);
    }

    /** @param array<mixed,mixed> $content */
    public function jsonResponse(array $content = [], int $status = HttpStatus::OK): ResponseInterface
    {
        $jsonContent = (string)json_encode($content, JSON_FORCE_OBJECT);

        return $this->rawResponse($jsonContent, $status, 'application/json');
    }

    public function notFoundResponse(string $content = ''): ResponseInterface
    {
        return $this->rawResponse($content, HttpStatus::NOT_FOUND);
    }

    public function accessDeniedResponse(string $content = ''): ResponseInterface
    {
        return $this->rawResponse($content, HttpStatus::FORBIDDEN);
    }

    public function serverErrorResponse(Throwable $exception): ResponseInterface
    {
        $message = sprintf(
            "Error: %s on file %s in line %d",
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
        );

        return $this->rawResponse($message, HttpStatus::INTERNAL_SERVER_ERROR);
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    private function rawResponse(
        string $content = '',
        int $status = HttpStatus::OK,
        string $mimeType = ''
    ): ResponseInterface {
        $response = $this->factory->createResponse($status, HttpStatus::reason($status));

        if ($mimeType !== '') {
            $response = $response->withHeader('Content-type', $mimeType);
        }

        if ($content == '') {
            return $response;
        }

        return $response->withBody(
            $this->factory->createStream($content)
        );
    }
}
