<?php

declare(strict_types=1);

namespace Freep\Application\Adapter\HttpFactory;

use Freep\Application\Http\HttpFactory;
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;
use Laminas\Diactoros\UriFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

/**
 * Para usar esse adaptador, é preciso instalar a seguinte biblioteca:
 * laminas/laminas-diactoros
 */
class DiactorosHttpFactory implements HttpFactory
{
    public function createRequestFromGlobals(): ServerRequestInterface
    {
        return (new ServerRequestFactory())->fromGlobals();
    }

    public function createRequest(string $method, $uri): RequestInterface
    {
        return (new RequestFactory())->createRequest($method, $uri);
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return (new ResponseFactory())->createResponse($code, $reasonPhrase);
    }

    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return (new ServerRequestFactory())->createServerRequest($method, $uri, $serverParams);
    }

    public function createStream(string $content = ''): StreamInterface
    {
        return (new StreamFactory())->createStream($content);
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return (new StreamFactory())->createStreamFromFile($filename, $mode);
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return (new StreamFactory())->createStreamFromResource($resource);
    }

    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface
    {
        return (new UploadedFileFactory())->createUploadedFile(
            $stream,
            $size,
            $error,
            $clientFilename,
            $clientMediaType
        );
    }

    public function createUri(string $uri = ''): UriInterface
    {
        return (new UriFactory())->createUri($uri);
    }
}
