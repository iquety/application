<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use Exception;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Application\Http\HttpStatus;
use Psr\Http\Message\ResponseInterface;
use Tests\Unit\TestCase;

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
        $responseFactory = new HttpResponseFactory(
            $this->adapterFactory(),
            $this->adapterFactory()->createRequestFromGlobals()
        );

        $response = $responseFactory->response('', HttpStatus::OK);

        $this->assertSame('', (string)$response->getBody());
        $this->assertTrue($response->hasHeader('Content-type'));
        $this->assertSame('text/html', $response->getHeaderLine('Content-type'));
        $this->assertSame(HttpStatus::OK->value, $response->getStatusCode());
    }

    /** @return array<string,array<int|string>> */
    public function contentProvider(): array
    {
        $statusList = [
            100 => HttpStatus::CONTINUE,
            101 => HttpStatus::SWITCHING_PROTOCOLS,
            102 => HttpStatus::PROCESSING,
            103 => HttpStatus::EARLY_HINTS,
            200 => HttpStatus::OK,
            201 => HttpStatus::CREATED,
            202 => HttpStatus::ACCEPTED,
            203 => HttpStatus::NON_AUTHORITATIVE_INFORMATION,
            204 => HttpStatus::NO_CONTENT,
            205 => HttpStatus::RESET_CONTENT,
            206 => HttpStatus::PARTIAL_CONTENT,
            207 => HttpStatus::MULTI_STATUS,
            208 => HttpStatus::ALREADY_REPORTED,
            226 => HttpStatus::IM_USED,
            300 => HttpStatus::MULTIPLE_CHOICES,
            301 => HttpStatus::MOVED_PERMANENTLY,
            302 => HttpStatus::FOUND,
            303 => HttpStatus::SEE_OTHER,
            304 => HttpStatus::NOT_MODIFIED,
            305 => HttpStatus::USE_PROXY,
            // Depreciado https://www.rfc-editor.org/rfc/rfc2616#section-10.3.7
            // 306 => SWITCH_PROXY
            307 => HttpStatus::TEMPORARY_REDIRECT,
            308 => HttpStatus::PERMANENTLY_REDIRECT,
            400 => HttpStatus::BAD_REQUEST,
            401 => HttpStatus::UNAUTHORIZED,
            402 => HttpStatus::PAYMENT_REQUIRED,
            403 => HttpStatus::FORBIDDEN,
            404 => HttpStatus::NOT_FOUND,
            405 => HttpStatus::METHOD_NOT_ALLOWED,
            406 => HttpStatus::NOT_ACCEPTABLE,
            407 => HttpStatus::PROXY_AUTHENTICATION_REQUIRED,
            408 => HttpStatus::REQUEST_TIMEOUT,
            409 => HttpStatus::CONFLICT,
            410 => HttpStatus::GONE,
            411 => HttpStatus::LENGTH_REQUIRED,
            412 => HttpStatus::PRECONDITION_FAILED,
            413 => HttpStatus::REQUEST_ENTITY_TOO_LARGE,
            414 => HttpStatus::REQUEST_URI_TOO_LONG,
            415 => HttpStatus::UNSUPPORTED_MEDIA_TYPE,
            416 => HttpStatus::REQUESTED_RANGE_NOT_SATISFIABLE,
            417 => HttpStatus::EXPECTATION_FAILED,
            418 => HttpStatus::I_AM_A_TEAPOT,
            421 => HttpStatus::MISDIRECTED_REQUEST,
            422 => HttpStatus::UNPROCESSABLE_ENTITY,
            423 => HttpStatus::LOCKED,
            424 => HttpStatus::FAILED_DEPENDENCY,
            425 => HttpStatus::TOO_EARLY,
            426 => HttpStatus::UPGRADE_REQUIRED,
            428 => HttpStatus::PRECONDITION_REQUIRED,
            429 => HttpStatus::TOO_MANY_REQUESTS,
            431 => HttpStatus::REQUEST_HEADER_FIELDS_TOO_LARGE,
            451 => HttpStatus::UNAVAILABLE_FOR_LEGAL_REASONS,
            500 => HttpStatus::INTERNAL_SERVER_ERROR,
            501 => HttpStatus::NOT_IMPLEMENTED,
            502 => HttpStatus::BAD_GATEWAY,
            503 => HttpStatus::SERVICE_UNAVAILABLE,
            504 => HttpStatus::GATEWAY_TIMEOUT,
            505 => HttpStatus::VERSION_NOT_SUPPORTED,
            506 => HttpStatus::VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL,
            507 => HttpStatus::INSUFFICIENT_STORAGE,
            508 => HttpStatus::LOOP_DETECTED,
            510 => HttpStatus::NOT_EXTENDED,
            511 => HttpStatus::NETWORK_AUTHENTICATION_REQUIRED,
        ];

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
        $responseFactory = new HttpResponseFactory(
            $this->adapterFactory(),
            $this->adapterFactory()->createRequestFromGlobals()
        );

        $response = $responseFactory->response('', $httpStatus);

        $this->assertEquals('', (string)$response->getBody());
        $this->assertEquals($httpStatus, $response->getStatusCode());
        $this->assertFalse($response->hasHeader('Content-type'));
        $this->assertSame('text/html', $response->getHeaderLine('Content-type'));
    }

    // /**
    //  * @test
    //  * @dataProvider contentProvider
    //  */
    // public function withStatus(int $httpStatus, string $body): void
    // {
    //     $responseFactory = new HttpResponseFactory($this->adapterFactory());

    //     $response = $responseFactory->response($body, $httpStatus);

    //     $this->assertEquals($body, (string)$response->getBody());
    //     $this->assertEquals($httpStatus, $response->getStatusCode());
    //     $this->assertFalse($response->hasHeader('Content-type'));
    // }

    // /**
    //  * @test
    //  * @dataProvider contentProvider
    //  */
    // public function withStatusAndEmptyMime(int $httpStatus, string $body): void
    // {
    //     $responseFactory = new HttpResponseFactory($this->adapterFactory());

    //     $response = $responseFactory->response($body, $httpStatus, '');

    //     $this->assertEquals($body, (string)$response->getBody());
    //     $this->assertEquals($httpStatus, $response->getStatusCode());
    //     $this->assertFalse($response->hasHeader('Content-type'));
    // }

    // /**
    //  * @test
    //  * @dataProvider contentProvider
    //  */
    // public function withMimeType(int $httpStatus, string $body): void
    // {
    //     $responseFactory = new HttpResponseFactory($this->adapterFactory());

    //     $response = $responseFactory->response($body, $httpStatus, 'text/html');

    //     $this->assertEquals($body, (string)$response->getBody());
    //     $this->assertEquals($httpStatus, $response->getStatusCode());
    //     $this->assertTrue($response->hasHeader('Content-type'));
    // }

    // /**
    //  * @test
    //  * @dataProvider contentProvider
    //  */
    // public function jsonResponse(int $httpStatus): void
    // {
    //     $responseFactory = new HttpResponseFactory($this->adapterFactory());

    //     $content = ['naitis' => ''];

    //     $response = $responseFactory->jsonResponse($content, $httpStatus);

    //     $this->assertEquals($httpStatus, $response->getStatusCode());
    //     $this->assertEquals(
    //         json_encode($content, JSON_FORCE_OBJECT),
    //         (string)$response->getBody()
    //     );
    //     $this->assertTrue($response->hasHeader('Content-type'));
    //     $this->assertEquals(['application/json'], $response->getHeader('Content-type'));
    // }

    // /** @test */
    // public function notFoundResponse(): void
    // {
    //     $responseFactory = new HttpResponseFactory($this->adapterFactory());

    //     $response = $responseFactory->notFoundResponse('monomo');

    //     $this->assertInstanceOf(ResponseInterface::class, $response);

    //     $this->assertEquals(404, $response->getStatusCode());
    //     $this->assertEquals('monomo', (string)$response->getBody());
    //     $this->assertFalse($response->hasHeader('Content-type'));
    // }

    // /** @test */
    // public function accessDeniedResponse(): void
    // {
    //     $responseFactory = new HttpResponseFactory($this->adapterFactory());

    //     $response = $responseFactory->accessDeniedResponse('monomo');

    //     $this->assertInstanceOf(ResponseInterface::class, $response);

    //     $this->assertEquals(403, $response->getStatusCode());
    //     $this->assertEquals('monomo', (string)$response->getBody());
    //     $this->assertFalse($response->hasHeader('Content-type'));
    // }

    // /** @test */
    // public function serverErrorResponse(): void
    // {
    //     $responseFactory = new HttpResponseFactory($this->adapterFactory());

    //     $response = $responseFactory->serverErrorResponse(new Exception('monomo'));

    //     $this->assertInstanceOf(ResponseInterface::class, $response);

    //     $this->assertEquals(500, $response->getStatusCode());
    //     $this->assertStringContainsString('Error: monomo on file', (string)$response->getBody());
    //     $this->assertFalse($response->hasHeader('Content-type'));
    // }
}
