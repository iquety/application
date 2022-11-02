<?php

declare(strict_types=1);

namespace Tests\Http;

use Exception;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Application\Http\HttpStatus;
use Iquety\Injection\Container;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
abstract class HttpResponseFactoryTestCase extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    abstract public function httpFactory(): HttpFactory;

    /** @return array<string,array<int,mixed>> */
    public function responseProvider(): array
    {
        return [
            'complete' => ['monomono', HttpStatus::HTTP_CREATED, 'text/html'],
            'empty mime' => ['monomono', HttpStatus::HTTP_CREATED, ''],
            'empty body' => ['', HttpStatus::HTTP_CREATED, 'text/html'],
            'empty body and mime' => ['', HttpStatus::HTTP_CREATED, '']
        ];
    }

    /**
     * @test
     * @dataProvider responseProvider
     */
    public function responseAdapters(string $content, int $status, string $mime): void
    {
        $responseFactory = new HttpResponseFactory($this->httpFactory());

        $response = $responseFactory->response($content, $status, $mime);

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals($content, $response->getBody()->getContents());
        $this->assertEquals($status, $response->getStatusCode());

        if ($mime === '') {
            $this->assertFalse($response->hasHeader('Content-type'));
            $this->assertEquals('', $response->getHeaderLine('Content-type'));
        }

        if ($mime !== '') {
            $this->assertTrue($response->hasHeader('Content-type'));
            $this->assertEquals($mime, $response->getHeaderLine('Content-type'));
        }
    }

    /**
     * @test
     * @dataProvider responseProvider
     */
    public function jsonResponse(string $content, int $status): void
    {
        $responseFactory = new HttpResponseFactory($this->httpFactory());

        $content = ['content' => $content];
        $jsonContent = json_encode($content, JSON_FORCE_OBJECT);

        $response = $responseFactory->jsonResponse($content, $status);

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals($jsonContent, $response->getBody()->getContents());
        $this->assertEquals(['application/json'], $response->getHeader('Content-type'));
    }

    /** @test */
    public function notFoundResponse(): void
    {
        $responseFactory = new HttpResponseFactory($this->httpFactory());

        $response = $responseFactory->notFoundResponse('monomo');

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('monomo', $response->getBody()->getContents());
        $this->assertFalse($response->hasHeader('Content-type'));
    }

    /** @test */
    public function accessDeniedResponse(): void
    {
        $responseFactory = new HttpResponseFactory($this->httpFactory());

        $response = $responseFactory->accessDeniedResponse('monomo');

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals('monomo', $response->getBody()->getContents());
        $this->assertFalse($response->hasHeader('Content-type'));
    }

    /** @test */
    public function serverErrorResponse(): void
    {
        $responseFactory = new HttpResponseFactory($this->httpFactory());

        $response = $responseFactory->serverErrorResponse(new Exception('monomo'));

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('monomo', $response->getBody()->getContents());
        $this->assertFalse($response->hasHeader('Content-type'));
    }
}
