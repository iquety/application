<?php

declare(strict_types=1);

namespace Iquety\Application\Http;

use InvalidArgumentException;
use Iquety\Application\Environment;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleXMLElement;
use Throwable;

class HttpResponseFactory
{
    private HttpMime $mimeType;

    private Environment $environment;

    public function __construct(
        private HttpFactory $factory,
        private ServerRequestInterface $serverRequest
    ) {
        $this->mimeType = HttpMime::makeBy(
            $this->serverRequest->getHeaderLine('Accept')
        );

        $this->environment = Environment::makeBy(
            $this->serverRequest->getHeaderLine('Environment')
        );
    }

    public function notFoundResponse(array|string $content = ''): ResponseInterface
    {
        return $this->response($content, HttpStatus::NOT_FOUND);
    }

    public function accessDeniedResponse(array|string $content = ''): ResponseInterface
    {
        return $this->response($content, HttpStatus::FORBIDDEN);
    }

    public function serverErrorResponse(Throwable $exception): ResponseInterface
    {
        if ($this->environment === Environment::DEVELOPMENT) {
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

    public function response(array|string $content, HttpStatus $status): ResponseInterface
    {
        $response = $this->factory->createResponse(
            $status->value,
            HttpStatus::reason($status->value)
        );

        $response = $response->withHeader('Content-type', $this->mimeType->value);

        if ($content === '') {
            return $response;
        }

        $resolvedContent = match ($this->mimeType) {
            HttpMime::HTML => $this->makeHtmlResponse($content),
            HttpMime::JSON => $this->makeJsonResponse($content),
            HttpMime::TEXT => $this->makeTextResponse($content),
            HttpMime::XML  => $this->makeXmlResponse($content),
            default        => $this->makeHtmlResponse($content)
        };

        return $response->withBody(
            $this->factory->createStream($resolvedContent)
        );
    }

    private function makeHtmlResponse(array|string $content): string
    {
        if (is_array($content) === true) {
            throw new InvalidArgumentException('An Html response must be textual content');
        }

        return $content;
    }

    private function makeJsonResponse(array|string $content): string
    {
        if (is_string($content) === true) {
            $content = [ 'content' => $content ];
        }

        return (string)json_encode($content, JSON_FORCE_OBJECT);
    }

    private function makeTextResponse(array|string $content): string
    {
        if (is_array($content) === true) {
            throw new InvalidArgumentException('An text response must be textual content');
        }

        return $content;
    }

    private function makeXmlResponse(array|string $content): string
    {
        if (is_string($content) === true) {
            $content = [ 'content' => $content ];
        }

        $mainElement = new SimpleXMLElement('<root/>');

        return $this->arrayToXml($content, $mainElement);
    }

    private function arrayToXml(array $content, ?SimpleXMLElement $element): string
    {
        foreach ($content as $tag => $value) {
            if (is_array($value) === true) {
                $this->arrayToXml($value, $element->addChild((string)$tag));

                continue;
            }

            $element->addChild((string)$tag, (string)$value);
        }

        return $element->asXML();
    }
}
