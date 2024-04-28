<?php

declare(strict_types=1);

namespace Iquety\Application\Adapter\HttpFactory;

use Iquety\Application\Http\HttpFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

/**
 * Para usar esse adaptador, é preciso instalar a seguinte biblioteca:
 * laminas/laminas-diactoros
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MemoryHttpFactory implements HttpFactory
{
    public function createRequestFromGlobals(): ServerRequestInterface
    {
        return new class implements ServerRequestInterface {
            // MessageInterface

            public function getProtocolVersion() { return '1.1'; }

            public function withProtocolVersion(string $version) { return $this; }

            public function getHeaders() { return [[]]; }

            public function hasHeader(string $name) { return false; }

            public function getHeader(string $name) { return []; }

            public function getHeaderLine(string $name) { return ''; }

            public function withHeader(string $name, $value) { return $this; }

            public function withAddedHeader(string $name, $value) { return $this; }

            public function withoutHeader(string $name) { return $this; }

            public function getBody() { return ''; } // StreamInterface

            public function withBody(StreamInterface $body) { return $this; }
            
            // RequestInterface

            public function getRequestTarget() { return ''; }

            public function withRequestTarget(string $requestTarget) { return $this; }

            public function getMethod() { return 'GET'; }

            public function withMethod(string $method) { return $this; }

            public function getUri() { return ''; } // UriInterface

            public function withUri(UriInterface $uri, bool $preserveHost = false) { return $this; }

            // ServerRequestInterface
            
            public function getServerParams() { return []; }

            public function getCookieParams() { return []; }

            public function withCookieParams(array $cookies) { return $this; }

            public function getQueryParams() { return []; }

            public function withQueryParams(array $query) { return $this; }

            public function getUploadedFiles() { return []; }

            public function withUploadedFiles(array $uploadedFiles) { return $this; }

            public function getParsedBody() { return null; }

            public function withParsedBody($data) { return $this; }

            public function getAttributes() { return []; }

            public function getAttribute(string $name, $default = null) { return ''; }

            public function withAttribute(string $name, $value) { return $this; }

            public function withoutAttribute(string $name) { return $this; }
        };
    }

    public function createRequest(string $method, $uri): RequestInterface
    {
        return new class implements RequestInterface {
            public function getProtocolVersion() { return '1.1'; }

            public function withProtocolVersion(string $version) { return $this; }

            public function getHeaders() { return [[]]; }

            public function hasHeader(string $name) { return false; }

            public function getHeader(string $name) { return []; }

            public function getHeaderLine(string $name) { return ''; }

            public function withHeader(string $name, $value) { return $this; }

            public function withAddedHeader(string $name, $value) { return $this; }

            public function withoutHeader(string $name) { return $this; }

            public function getBody() { return ''; } // StreamInterface

            public function withBody(StreamInterface $body) { return $this; }

            public function getRequestTarget() { return ''; }

            public function withRequestTarget(string $requestTarget) { return $this; }

            public function getMethod() { return 'GET'; }

            public function withMethod(string $method) { return $this; }

            public function getUri() { return ''; } // UriInterface

            public function withUri(UriInterface $uri, bool $preserveHost = false) { return $this; }
        };
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new class ($code, $reasonPhrase) implements ResponseInterface {

            private StreamInterface $body;

            public function __construct(private int $code = 200, private string $reasonPhrase = '')
            {
                $this->body = (new MemoryHttpFactory())->createStream('');
            }

            // MessageInterface

            public function getProtocolVersion() { return '1.1'; }

            public function withProtocolVersion(string $version) { return $this; }

            public function getHeaders() { return [[]]; }

            public function hasHeader(string $name) { return false; }

            public function getHeader(string $name) { return []; }

            public function getHeaderLine(string $name) { return ''; }

            public function withHeader(string $name, $value) { return $this; }

            public function withAddedHeader(string $name, $value) { return $this; }

            public function withoutHeader(string $name) { return $this; }

            public function getBody() { return $this->body; } // StreamInterface

            public function withBody(StreamInterface $body)
            { 
                $this->body = $body;

                return $this;
            }

            // ResponseInterface
            
            public function getStatusCode() { return $this->code; }

            public function withStatus(int $code, string $reasonPhrase = '')
            { 
                return new self($code, $reasonPhrase);
            }

            public function getReasonPhrase() { return $this->reasonPhrase; }
        };
    }

    /**
     * @param array<string,mixed> $serverParams
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return new class implements ServerRequestInterface {
            // MessageInterface

            public function getProtocolVersion() { return '1.1'; }

            public function withProtocolVersion(string $version) { return $this; }

            public function getHeaders() { return [[]]; }

            public function hasHeader(string $name) { return false; }

            public function getHeader(string $name) { return []; }

            public function getHeaderLine(string $name) { return ''; }

            public function withHeader(string $name, $value) { return $this; }

            public function withAddedHeader(string $name, $value) { return $this; }

            public function withoutHeader(string $name) { return $this; }

            public function getBody() { return ''; } // StreamInterface

            public function withBody(StreamInterface $body) { return $this; }
            
            // RequestInterface

            public function getRequestTarget() { return ''; }

            public function withRequestTarget(string $requestTarget) { return $this; }

            public function getMethod() { return 'GET'; }

            public function withMethod(string $method) { return $this; }

            public function getUri() { return ''; } // UriInterface

            public function withUri(UriInterface $uri, bool $preserveHost = false) { return $this; }

            // ServerRequestInterface
            
            public function getServerParams() { return []; }

            public function getCookieParams() { return []; }

            public function withCookieParams(array $cookies) { return $this; }

            public function getQueryParams() { return []; }

            public function withQueryParams(array $query) { return $this; }

            public function getUploadedFiles() { return []; }

            public function withUploadedFiles(array $uploadedFiles) { return $this; }

            public function getParsedBody() { return null; }

            public function withParsedBody($data) { return $this; }

            public function getAttributes() { return []; }

            public function getAttribute(string $name, $default = null) { return ''; }

            public function withAttribute(string $name, $value) { return $this; }

            public function withoutAttribute(string $name) { return $this; }
        };
    }

    public function createStream(string $content = ''): StreamInterface
    {
        return new class($content) implements StreamInterface {

            public function __construct(private string $content)
            {
            }

            public function __toString() { return $this->content; }

            public function close() {}

            public function detach() { return null; }

            public function getSize() { return 0; }

            public function tell() { return 0; }

            public function eof() { return false; }

            public function isSeekable() { return false; }

            public function seek(int $offset, int $whence = SEEK_SET) {}

            public function rewind() {}

            public function isWritable() { return false; }

            public function write(string $string) { return 0; }

            public function isReadable() { return false; }

            public function read(int $length) { return ''; }

            public function getContents() { return $this->content; }

            public function getMetadata(?string $key = null) { return null; }
        };
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return new class implements StreamInterface {
            public function __toString() { return ''; }

            public function close() {}

            public function detach() { return null; }

            public function getSize() { return 0; }

            public function tell() { return 0; }

            public function eof() { return false; }

            public function isSeekable() { return false; }

            public function seek(int $offset, int $whence = SEEK_SET) {}

            public function rewind() {}

            public function isWritable() { return false; }

            public function write(string $string) { return 0; }

            public function isReadable() { return false; }

            public function read(int $length) { return ''; }

            public function getContents() { return ''; }

            public function getMetadata(?string $key = null) { return null; }
        };
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return new class implements StreamInterface {
            public function __toString() { return ''; }

            public function close() {}

            public function detach() { return null; }

            public function getSize() { return 0; }

            public function tell() { return 0; }

            public function eof() { return false; }

            public function isSeekable() { return false; }

            public function seek(int $offset, int $whence = SEEK_SET) {}

            public function rewind() {}

            public function isWritable() { return false; }

            public function write(string $string) { return 0; }

            public function isReadable() { return false; }

            public function read(int $length) { return ''; }

            public function getContents() { return ''; }

            public function getMetadata(?string $key = null) { return null; }
        };
    }

    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface {
        return new class implements UploadedFileInterface {
            public function getStream() { return null;  } // StreamInterface

            public function moveTo(string $targetPath) { }
            
            public function getSize() { return 0; }
            
            public function getError() { return 0; }
            
            public function getClientFilename() { return null; }
            
            public function getClientMediaType() { return null; }
        };
    }

    public function createUri(string $uri = ''): UriInterface
    {
        return new class implements UriInterface {
            public function getScheme() { return ''; }

            public function getAuthority() { return ''; }

            public function getUserInfo() { return ''; }

            public function getHost() { return ''; }

            public function getPort() { return 80; }

            public function getPath() { return ''; }

            public function getQuery() { return ''; }

            public function getFragment() { return ''; }

            public function withScheme(string $scheme) { return $this; }

            public function withUserInfo(string $user, ?string $password = null) { return $this; }

            public function withHost(string $host) { return $this; }

            public function withPort(?int $port) { return $this; }

            public function withPath(string $path) { return $this; }

            public function withQuery(string $query) { return $this; }

            public function withFragment(string $fragment) { return $this; }

            public function __toString() { return ''; }
        };
    }
}
