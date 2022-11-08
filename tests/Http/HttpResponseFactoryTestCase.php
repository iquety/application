<?php

declare(strict_types=1);

namespace Tests\Http;

use Exception;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Application\Http\HttpStatus;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
abstract class HttpResponseFactoryTestCase extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    abstract public function adapterFactory(): HttpFactory;

    /** @test */
    public function withoutArguments(): void
    {
        $responseFactory = new HttpResponseFactory($this->adapterFactory());

        $response = $responseFactory->response();

        $this->assertEquals('', (string)$response->getBody());
        $this->assertEquals(HttpStatus::OK, $response->getStatusCode());
        $this->assertFalse($response->hasHeader('Content-type'));
    }

    public function contentProvider(): array
    {
        $list = [];

        foreach (array_keys(HttpStatus::all()) as $httpStatus) {
            $list["$httpStatus with body"]    = [ $httpStatus, 'body teste' ];
            $list["$httpStatus without body"] = [ $httpStatus, '' ];
        }

        return $list;
    }

    /** 
     * @test
     * @dataProvider contentProvider
     */
    public function withEmptyArgumens(int $httpStatus): void
    {
        $responseFactory = new HttpResponseFactory($this->adapterFactory());

        $response = $responseFactory->response('', $httpStatus, '');

        $this->assertEquals('', (string)$response->getBody());
        $this->assertEquals($httpStatus, $response->getStatusCode());
        $this->assertFalse($response->hasHeader('Content-type'));
    }

    /**
     * @test
     * @dataProvider contentProvider
     */
    public function withStatus(int $httpStatus, string $body): void
    {
        $responseFactory = new HttpResponseFactory($this->adapterFactory());

        $response = $responseFactory->response($body, $httpStatus);

        $this->assertEquals($body, (string)$response->getBody());
        $this->assertEquals($httpStatus, $response->getStatusCode());
        $this->assertFalse($response->hasHeader('Content-type'));
    }

    /**
     * @test
     * @dataProvider contentProvider
     */
    public function withStatusAndEmptyMime(int $httpStatus, string $body): void
    {
        $responseFactory = new HttpResponseFactory($this->adapterFactory());

        $response = $responseFactory->response($body, $httpStatus, '');

        $this->assertEquals($body, (string)$response->getBody());
        $this->assertEquals($httpStatus, $response->getStatusCode());
        $this->assertFalse($response->hasHeader('Content-type'));
    }

    /**
     * @test
     * @dataProvider contentProvider
     */
    public function withMimeType(int $httpStatus, string $body): void
    {
        $responseFactory = new HttpResponseFactory($this->adapterFactory());

        $response = $responseFactory->response($body, $httpStatus, 'text/html');

        $this->assertEquals($body, (string)$response->getBody());
        $this->assertEquals($httpStatus, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Content-type'));
    }

    /**
     * @test
     * @dataProvider contentProvider
     */
    public function jsonResponse(int $httpStatus, string $body): void
    {
        $responseFactory = new HttpResponseFactory($this->adapterFactory());

        $content = ['naitis' => ''];

        $response = $responseFactory->jsonResponse($content, $httpStatus);

        $this->assertEquals($httpStatus, $response->getStatusCode());
        $this->assertEquals(
            json_encode($content, JSON_FORCE_OBJECT),
            (string)$response->getBody()
        );
        $this->assertTrue($response->hasHeader('Content-type'));
        $this->assertEquals(['application/json'], $response->getHeader('Content-type'));
    }

    /** @test */
    public function notFoundResponse(): void
    {
        $responseFactory = new HttpResponseFactory($this->adapterFactory());

        $response = $responseFactory->notFoundResponse('monomo');

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('monomo', (string)$response->getBody());
        $this->assertFalse($response->hasHeader('Content-type'));
    }

    /** @test */
    public function accessDeniedResponse(): void
    {
        $responseFactory = new HttpResponseFactory($this->adapterFactory());

        $response = $responseFactory->accessDeniedResponse('monomo');

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals('monomo', (string)$response->getBody());
        $this->assertFalse($response->hasHeader('Content-type'));
    }

    /** @test */
    public function serverErrorResponse(): void
    {
        $responseFactory = new HttpResponseFactory($this->adapterFactory());

        $response = $responseFactory->serverErrorResponse(new Exception('monomo'));

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertStringContainsString('Error: monomo on file', (string)$response->getBody());
        $this->assertFalse($response->hasHeader('Content-type'));
    }
}
