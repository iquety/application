<?php

declare(strict_types=1);

namespace Tests\Http;

use Freep\Application\Application;
use Freep\Application\Http\HttpResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

class HttpResponseFactoryTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }
    
    /** @test */
    public function response(): void
    {
        $app = TestCase::applicationFactory();

        $response = (new HttpResponseFactory($app))->response('monomo', 201, 'text/html');

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(['text/html'], $response->getHeader('Content-type'));
        $this->assertEquals('monomo', $response->getBody()->getContents());
    }

    /** @test */
    public function responseWithEmptyHeader(): void
    {
        $app = TestCase::applicationFactory();

        $response = (new HttpResponseFactory($app))->response('monomo', 201, '');

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertFalse($response->hasHeader('Content-type'));
        $this->assertEquals('monomo', $response->getBody()->getContents());
    }

    /** @test */
    public function responseWithEmptyBody(): void
    {
        $app = TestCase::applicationFactory();

        $response = (new HttpResponseFactory($app))->response('', 201, 'text/html');

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(['text/html'], $response->getHeader('Content-type'));
        $this->assertEquals('', $response->getBody()->getContents());
    }

    /** @test */
    public function responseWithEmptyBodyAndHeader(): void
    {
        $app = TestCase::applicationFactory();

        $response = (new HttpResponseFactory($app))->response('', 201, '');

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertFalse($response->hasHeader('Content-type'));
        $this->assertEquals('', $response->getBody()->getContents());
    }

    /** @test */
    public function responseWithEmptyBodyAndNullHeader(): void
    {
        $app = TestCase::applicationFactory();

        $response = (new HttpResponseFactory($app))->response('', 201);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertFalse($response->hasHeader('Content-type'));
        $this->assertEquals('', $response->getBody()->getContents());
    }
}