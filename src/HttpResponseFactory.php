<?php

declare(strict_types=1);

namespace Iquety\Application;

use Iquety\Application\Environment;
use Iquety\Http\HttpFactory;
use Iquety\Http\HttpMime;
use Iquety\Http\HttpStatus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class HttpResponseFactory
{
    private HttpMime $mimeType;

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    public function __construct(
        private HttpFactory $factory,
        private ServerRequestInterface $serverRequest,
        private Environment $environment
    ) {
        $this->mimeType = $this->resolveAccept();
    }

    /** @param array<int|string,mixed>|string|ResponseInterface $content */
    public function notFoundResponse(array|string|ResponseInterface $content = ''): ResponseInterface
    {
        return $this->response($content, HttpStatus::NOT_FOUND);
    }

    /** @param array<int|string,mixed>|string|ResponseInterface $content */
    public function accessDeniedResponse(array|string|ResponseInterface $content = ''): ResponseInterface
    {
        return $this->response($content, HttpStatus::FORBIDDEN);
    }

    public function serverErrorResponse(Throwable $exception): ResponseInterface
    {
        if ($this->environment === Environment::PRODUCTION) {
            return $this->response(
                'An error occurred on the server side',
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }

        $content = sprintf(
            "Error: %s on file %s in line %d\n%s",
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        if (in_array($this->mimeType, [HttpMime::JSON, HttpMime::XML]) === true) {
            $content = [
                'message' => $exception->getMessage(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'trace'   => $exception->getTrace(),
            ];
        }

        return $this->response($content, HttpStatus::INTERNAL_SERVER_ERROR);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param array<int|string,mixed>|string|ResponseInterface $content
     */
    public function response(array|string|ResponseInterface $content, HttpStatus $status): ResponseInterface
    {
        if ($content instanceof ResponseInterface) {
            return $content;
        }

        if ($content === '') {
            return $this->createResponseEmpty($status);
        }

        return match ($this->mimeType) {
            HttpMime::JSON => $this->factory->createResponseJson($content, $status),
            HttpMime::TEXT => $this->factory->createResponseText($content, $status),
            HttpMime::XML => $this->factory->createResponseXml($content, $status),
            default => $this->factory->createResponseHtml($content, $status)
        };
    }

    private function createResponseEmpty(HttpStatus $status): ResponseInterface
    {
        $response = $this->factory->createResponse(
            $status->value,
            HttpStatus::from($status->value)->reason()
        );

        return $response->withHeader('Content-type', $this->mimeType->value);
    }

    private function resolveAccept(): HttpMime
    {
        $acceptHeader = $this->serverRequest->getHeaderLine('Accept');

        $mimeList = array_filter(explode(',', $acceptHeader));

        $resolved = HttpMime::HTML;

        foreach($mimeList as $mime) {
            // casos: application/xml;q=0.9
            $mime = preg_replace('/;.*/', '', $mime);

            if ($mime === '*/*') {
                $resolved = HttpMime::HTML;

                break;
            }

            $try = HttpMime::tryFrom($mime);

            if ($try !== null) {
                $resolved = $try;

                break;
            }
        }

        return $resolved;
    }
}
