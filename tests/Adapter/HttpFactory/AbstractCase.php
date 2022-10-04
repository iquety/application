<?php

declare(strict_types=1);

namespace Tests\Adapter\HttpFactory;

use Freep\Application\Http\HttpFactory;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
abstract class AbstractCase extends TestCase
{
    abstract protected function makeFactory(): HttpFactory;

    /** @test */
    public function createRequestFromGlobals(): void
    {
        $object = $this->makeFactory()->createRequestFromGlobals();

        $this->assertInstanceOf(ServerRequestInterface::class, $object);
    }

    /** @test */
    public function createRequest(): void
    {
        $object = $this->makeFactory()->createRequest('POST', '/user/33');

        $this->assertInstanceOf(RequestInterface::class, $object);
    }

    /** @test */
    public function createResponse(): void
    {
        $object = $this->makeFactory()->createResponse();

        $this->assertInstanceOf(ResponseInterface::class, $object);
    }

    /** @test */
    public function createServerRequest(): void
    {
        $object = $this->makeFactory()->createServerRequest('POST', '/user/33', []);

        $this->assertInstanceOf(ServerRequestInterface::class, $object);
    }

    /** @test */
    public function createServerRequestEmptyMethod(): void
    {
        $object = $this->makeFactory()->createServerRequest('', '/user/33', [
            'REQUEST_METHOD' => 'POST'
        ]);

        $this->assertInstanceOf(ServerRequestInterface::class, $object);
    }

    /** @test */
    public function createServerRequestException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot determine HTTP method');

        $this->makeFactory()->createServerRequest('', '/user/33', []);
    }

    /** @test */
    public function createStream(): void
    {
        $object = $this->makeFactory()->createStream('monomonomo');

        $this->assertInstanceOf(StreamInterface::class, $object);
    }

    /** @test */
    public function createStreamFromFile(): void
    {
        $object = $this->makeFactory()->createStreamFromFile(__DIR__ . '/streamfile.txt');

        $this->assertInstanceOf(StreamInterface::class, $object);
    }

    /** @test */
    public function createStreamFromResource(): void
    {
        /** @var resource $resource */
        $resource = fopen(__DIR__ . '/streamfile.txt', 'r');

        $object = $this->makeFactory()->createStreamFromResource($resource);

        $this->assertInstanceOf(StreamInterface::class, $object);
    }

    /** @test */
    public function createUploadedFile(): void
    {
        $stream = $this->makeFactory()->createStream('monomonom');
        $object = $this->makeFactory()->createUploadedFile($stream);

        $this->assertInstanceOf(UploadedFileInterface::class, $object);
    }

    /** @test */
    public function createUri(): void
    {
        $object = $this->makeFactory()->createUri('http://www.google.com/teste');

        $this->assertInstanceOf(UriInterface::class, $object);
    }
}
