<?php

declare(strict_types=1);

namespace Freep\Application\Adapter\HttpFactory;

use Freep\Application\Http\HttpFactory;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\UploadedFile;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\Utils;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

/**
 * Para usar esse adaptador, é preciso instalar a seguinte biblioteca:
 * guzzlehttp/guzzle
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class GuzzleHttpFactory implements HttpFactory
{
    public function createRequestFromGlobals(): ServerRequestInterface
    {
        return ServerRequest::fromGlobals();
    }

    public function createRequest(string $method, $uri): RequestInterface
    {
        return new Request($method, $uri);
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new Response($code, [], null, '1.1', $reasonPhrase);
    }

    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        $method = $method === '' && isset($serverParams['REQUEST_METHOD'])
            ? $serverParams['REQUEST_METHOD']
            : $method;

        if ($method === '') {
            throw new InvalidArgumentException('Cannot determine HTTP method');
        }

        return new ServerRequest($method, $uri, [], null, '1.1', $serverParams);
    }

    public function createStream(string $content = ''): StreamInterface
    {
        return Utils::streamFor($content);
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return $this->createStreamFromResource(Utils::tryFopen($filename, $mode));
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream($resource);
    }

    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface {
        if ($size === null) {
            $size = $stream->getSize();
        }

        return new UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }

    public function createUri(string $uri = ''): UriInterface
    {
        return new Uri($uri);
    }
}
