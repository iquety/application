<?php

declare(strict_types=1);

namespace Tests\Http;

use Exception;
use Freep\Application\Application;
use Freep\Application\Http\HttpResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class HttpResponseFactoryTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    public function responseProvider(): array
    {
        return [
            'complete' => ['monomono', 201, 'text/html'],
            'empty header' => ['monomono', 201, ''],
            'no header' => ['monomono', 201, null],
            'empty body' => ['', 201, 'text/html'],
            'empty body and empty header' => ['', 201, ''],
            'empty body and no header' => ['', 201, null],
        ];
    }

    /**
     * @test
     * @dataProvider responseProvider
     */
    public function response(string $content, int $status, ?string $mime): void
    {
        $app = TestCase::applicationFactory();

        $response = (new HttpResponseFactory($app))->response($content, $status, $mime);

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals($content, $response->getBody()->getContents());

        if ($mime === null || $mime === '') {
            $this->assertFalse($response->hasHeader('Content-type'));
            return;
        }

        $this->assertEquals([$mime], $response->getHeader('Content-type'));
    }

    /**
     * @test
     * @dataProvider responseProvider
     */
    public function jsonResponse(string $content, int $status): void
    {
        $app = TestCase::applicationFactory();

        $content = ['content' => $content];
        $jsonContent = json_encode($content, JSON_FORCE_OBJECT);

        $response = (new HttpResponseFactory($app))->jsonResponse($content, $status);

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals($jsonContent, $response->getBody()->getContents());
        $this->assertEquals(['application/json'], $response->getHeader('Content-type'));
    }

    /** @test */
    public function notFoundResponse(): void
    {
        $app = TestCase::applicationFactory();

        $response = (new HttpResponseFactory($app))->notFoundResponse('monomo');

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('monomo', $response->getBody()->getContents());
        $this->assertFalse($response->hasHeader('Content-type'));
    }

    /** @test */
    public function accessDeniedResponse(): void
    {
        $app = TestCase::applicationFactory();

        $response = (new HttpResponseFactory($app))->accessDeniedResponse('monomo');

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals('monomo', $response->getBody()->getContents());
        $this->assertFalse($response->hasHeader('Content-type'));
    }

    /** @test */
    public function serverErrorResponse(): void
    {
        $app = TestCase::applicationFactory();

        $response = (new HttpResponseFactory($app))->serverErrorResponse(new Exception('monomo'));

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('monomo', $response->getBody()->getContents());
        $this->assertFalse($response->hasHeader('Content-type'));
    }
}
