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

    /** @SuppressWarnings(PHPMD.StaticAccess) */
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
            // default        => $this->makeHtmlResponse($content)
        };

        return $response->withBody(
            $this->factory->createStream($resolvedContent)
        );
    }

    /** @param array<int|string,mixed>|string $content */
    private function makeHtmlResponse(array|string $content): string
    {
        if (is_array($content) === true) {
            return $this->makeTextResponse($content);
        }

        return $content;
    }

    /** @param array<int|string,mixed>|string $content */
    private function makeJsonResponse(array|string $content): string
    {
        if (is_string($content) === true) {
            $content = [ 'content' => $content ];
        }

        return (string)json_encode($content, JSON_FORCE_OBJECT);
    }

    /** @param array<int|string,mixed>|string $content */
    private function makeTextResponse(array|string $content, int $level = 0): string
    {
        if (is_array($content) === false) {
            return $content;
        }

        $padding = str_repeat('  ', $level);

        $textualContent = '';

        foreach($content as $name => $value) {
            if (is_array($value) === true) {
                $textualContent .= $this->makeTextResponse($value, $level+1);
                continue;
            }

            $textualContent .= "$padding$name=$value\n";
        }

        return $textualContent;
    }

    /** @param array<int|string,mixed>|string $content */
    private function makeXmlResponse(array|string $content): string
    {
        if (is_string($content) === true) {
            $content = [ 'content' => $content ];
        }

        $mainElement = new SimpleXMLElement('<root/>');

        return $this->arrayToXml($content, $mainElement);
    }

    /** @param array<int|string,mixed> $content */
    private function arrayToXml(array $content, SimpleXMLElement $element): string
    {
        foreach ($content as $tag => $value) {
            if (is_numeric($tag) === true) {
                $tag = 'item';
            }

            if (is_array($value) === true) {
                $this->arrayToXml($value, $element->addChild((string)$tag));

                continue;
            }

            $element->addChild(
                (string)$tag,
                (string)htmlentities((string)$value)
            );
        }

        return (string)$element->asXML();
    }
}
