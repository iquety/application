<?php

declare(strict_types=1);

namespace Tests\HttpResponse;

use Exception;
use Iquety\Application\Environment;
use Iquety\Application\HttpResponseFactory;
use Iquety\Http\HttpFactory;
use Iquety\Http\HttpMime;
use Iquety\Http\HttpStatus;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
abstract class HttpResponseCase extends TestCase
{
    abstract public function adapterFactory(): HttpFactory;

    /** @test */
    public function withoutArguments(): void
    {
        $responseFactory = new HttpResponseFactory(
            $this->adapterFactory(),
            $this->adapterFactory()->createRequestFromGlobals(),
            Environment::STAGE
        );

        $response = $responseFactory->response('', HttpStatus::OK);

        $this->assertSame('', (string)$response->getBody());
        $this->assertTrue($response->hasHeader('Content-type'));
        $this->assertSame('text/html', $response->getHeaderLine('Content-type'));
        $this->assertSame(HttpStatus::OK->value, $response->getStatusCode());
    }

    /** @return array<string,array<int,mixed>> */
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

        // $statusList = [
        //     100 => HttpStatus::CONTINUE,
        // ];

        $list = [];

        foreach ($statusList as $httpStatus) {
            $list[$httpStatus->value . " with body"]    = [ $httpStatus, 'body teste' ];
            $list[$httpStatus->value . " without body"] = [ $httpStatus, '' ];
        }

        return $list;
    }

    /**
     * @test
     * @dataProvider contentProvider
     */
    public function withEmptyArgumens(HttpStatus $httpStatus, string $body): void
    {
        $responseFactory = new HttpResponseFactory(
            $this->adapterFactory(),
            $this->adapterFactory()->createRequestFromGlobals(),
            Environment::STAGE
        );

        $response = $responseFactory->response($body, $httpStatus);

        $this->assertSame($body, (string)$response->getBody());
        $this->assertSame($httpStatus->value, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Content-type'));
        $this->assertSame('text/html', $response->getHeaderLine('Content-type'));
    }

    /**
     * @test
     * @dataProvider contentProvider
     */
    public function withStatus(HttpStatus $httpStatus, string $body): void
    {
        $responseFactory = new HttpResponseFactory(
            $this->adapterFactory(),
            $this->adapterFactory()->createRequestFromGlobals(),
            Environment::STAGE
        );

        $response = $responseFactory->response($body, $httpStatus);

        $this->assertSame($body, (string)$response->getBody());
        $this->assertSame($httpStatus->value, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Content-type'));
        $this->assertSame('text/html', $response->getHeaderLine('Content-type'));
    }

    /**
     * @test
     * @dataProvider contentProvider
     */
    public function withStatusAndEmptyMime(HttpStatus $httpStatus, string $body): void
    {
        $responseFactory = new HttpResponseFactory(
            $this->adapterFactory(),
            $this->adapterFactory()->createRequestFromGlobals(),
            Environment::STAGE
        );

        $response = $responseFactory->response($body, $httpStatus);

        $this->assertSame($body, (string)$response->getBody());
        $this->assertSame($httpStatus->value, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Content-type'));
        $this->assertSame('text/html', $response->getHeaderLine('Content-type'));
    }

    /** @return array<string,array<int,mixed>> */
    public function contentAcceptProvider(): array
    {
        $list = [];

        $originalList = $this->contentProvider();

        $mimeList = [
            HttpMime::HTML,
            HttpMime::JSON,
            HttpMime::TEXT,
            HttpMime::XML
        ];

        foreach ($originalList as $index => $payload) {
            foreach ($mimeList as $mime) {
                $label = $index . ' ' . $mime->value;

                $httpStatus = $payload[0];
                $body       = $payload[1];

                $responseBody = '';

                if ($body !== '') {
                    $responseBody = match ($mime) {
                        HttpMime::JSON => sprintf(
                            json_encode((string)$body),
                        ),
                        HttpMime::XML => sprintf(
                            "<?xml version=\"1.0\"?>\n<root><content>%s</content></root>\n",
                            $body
                        ),
                        default => $body
                    };
                }

                $list[$label] = [
                    $httpStatus,
                    $body,
                    $mime,
                    $responseBody
                ];
            }
        }

        return $list;
    }

    /**
     * @test
     * @dataProvider contentAcceptProvider
     */
    public function withSingleMimeType(
        HttpStatus $httpStatus,
        string $contentBody,
        HttpMime $acceptMime,
        string $responseBody
    ): void {
        $serverRequest = $this->adapterFactory()
            ->createRequestFromGlobals()
            ->withAddedHeader('Accept', $acceptMime->value);

        $responseFactory = new HttpResponseFactory(
            $this->adapterFactory(),
            $serverRequest,
            Environment::STAGE
        );

        $response = $responseFactory->response($contentBody, $httpStatus);

        $this->assertSame($httpStatus->value, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Content-type'));
        $this->assertSame($acceptMime->value, $response->getHeaderLine('Content-type'));
        $this->assertSame($responseBody, (string)$response->getBody());
    }

    /**
     * @return array<string,array<int,mixed>>
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function contentAcceptMultipleProvider(): array
    {
        $list = [];

        $list['json 1'] = [
            'application/json,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            HttpMime::JSON
        ];

        $list['json 2'] = [
            'application/xhtml+xml,application/json,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            HttpMime::JSON
        ];

        $list['json 3'] = [
            'application/xhtml+xml,image/png,application/json,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            HttpMime::JSON
        ];

        $list['json param'] = [
            'application/xhtml+xml,image/png,application/json;q=0.9,image/avif,image/webp,*/*;q=0.8',
            HttpMime::JSON
        ];

        $list['xml 1'] = [
            'application/xml,text/html,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            HttpMime::XML
        ];

        $list['xml 2'] = [
            'image/png,application/xml,text/html,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            HttpMime::XML
        ];

        $list['xml 3'] = [
            'image/png,application/xpix,application/xml,text/html;q=0.9,image/avif,image/webp,*/*;q=0.8',
            HttpMime::XML
        ];

        $list['xml param'] = [
            'image/png,application/xpix,application/xml;q=0.9,text/html;q=0.9,image/avif,image/webp,*/*;q=0.8',
            HttpMime::XML
        ];

        $list['html 1'] = [
            'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            HttpMime::HTML
        ];

        $list['html 2'] = [
            'application/xhtml+xml,text/html,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            HttpMime::HTML
        ];

        $list['html 3'] = [
            'application/xhtml+xml,image/png,text/html,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            HttpMime::HTML
        ];

        $list['html param'] = [
            'application/xhtml+xml,image/png,text/html;q=0.9,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            HttpMime::HTML
        ];

        $list['text 1'] = [
            'text/plain,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            HttpMime::TEXT
        ];

        $list['text 2'] = [
            'application/xhtml+xml,text/plain,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            HttpMime::TEXT
        ];

        $list['text 3'] = [
            'application/xhtml+xml,image/png,text/plain,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            HttpMime::TEXT
        ];

        $list['text param'] = [
            'application/xhtml+xml,image/png,text/plain;q=0.9,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            HttpMime::TEXT
        ];

        $list = [];
        $list['default 1'] = [
            '*/*,application/xhtml+xml,image/png;q=0.9,image/avif,image/webp;q=0.8',
            HttpMime::HTML
        ];

        $list['default 2'] = [
            'application/xhtml+xml,*/*,image/png;q=0.9,image/avif,image/webp;q=0.8',
            HttpMime::HTML
        ];

        $list['default last'] = [
            'application/xhtml+xml,image/png;q=0.9,image/avif,image/webp;q=0.8,*/*',
            HttpMime::HTML
        ];

        return $list;
    }

    /**
     * @test
     * @dataProvider contentAcceptMultipleProvider
     */
    public function withMultipleMimeTypes(string $acceptHeader, HttpMime $acceptResolved): void
    {
        $serverRequest = $this->adapterFactory()
            ->createRequestFromGlobals()
            ->withAddedHeader('Accept', $acceptHeader);

        $responseFactory = new HttpResponseFactory(
            $this->adapterFactory(),
            $serverRequest,
            Environment::STAGE
        );

        $response = $responseFactory->response('xxxxxx', HttpStatus::OK);

        $this->assertTrue($response->hasHeader('Content-type'));
        $this->assertSame($acceptResolved->value, $response->getHeaderLine('Content-type'));
    }

    /** @return array<string,array<int,mixed>> */
    public function contentNotFoundProvider(): array
    {
        $list = [];

        $originalList = $this->contentAcceptProvider();

        foreach ($originalList as $index => $payload) {
            array_shift($payload);

            $list[$index] = $payload;
        }

        return $list;
    }

    /**
     * @test
     * @dataProvider contentNotFoundProvider
     */
    public function notFoundResponse(
        string $contentBody,
        HttpMime $acceptMime,
        string $responseBody
    ): void {
        $serverRequest = $this->adapterFactory()
            ->createRequestFromGlobals()
            ->withAddedHeader('Accept', $acceptMime->value);

        $responseFactory = new HttpResponseFactory(
            $this->adapterFactory(),
            $serverRequest,
            Environment::STAGE
        );

        $response = $responseFactory->notFoundResponse($contentBody);

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Content-type'));
        $this->assertSame($acceptMime->value, $response->getHeaderLine('Content-type'));
        $this->assertSame($responseBody, (string)$response->getBody());
    }

    /**
     * @test
     * @dataProvider contentNotFoundProvider
     */
    public function accessDeniedResponse(
        string $contentBody,
        HttpMime $acceptMime,
        string $responseBody
    ): void {
        $serverRequest = $this->adapterFactory()
            ->createRequestFromGlobals()
            ->withAddedHeader('Accept', $acceptMime->value);

        $responseFactory = new HttpResponseFactory(
            $this->adapterFactory(),
            $serverRequest,
            Environment::STAGE
        );

        $response = $responseFactory->accessDeniedResponse($contentBody);

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Content-type'));
        $this->assertSame($acceptMime->value, $response->getHeaderLine('Content-type'));
        $this->assertSame($responseBody, (string)$response->getBody());
    }

    /** @return array<string,array<int,mixed>> */
    public function contentErrorProvider(): array
    {
        $list = [];

        $list['html'] = [HttpMime::HTML];
        $list['json'] = [HttpMime::JSON];
        $list['text'] = [HttpMime::TEXT];
        $list['xml'] = [HttpMime::XML];

        return $list;
    }

    /**
     * @test
     * @dataProvider contentErrorProvider
     */
    public function serverErrorResponseRealMessage(HttpMime $acceptMime): void
    {
        $serverRequest = $this->adapterFactory()
            ->createRequestFromGlobals()
            ->withAddedHeader('Accept', $acceptMime->value);

        $responseFactory = new HttpResponseFactory(
            $this->adapterFactory(),
            $serverRequest,
            Environment::STAGE
        );

        $response = $responseFactory->serverErrorResponse(new Exception('monomo'));

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertStringContainsString("monomo", (string)$response->getBody());
        $this->assertTrue($response->hasHeader('Content-type'));
        $this->assertSame($acceptMime->value, $response->getHeaderLine('Content-type'));
    }

    /**
     * @test
     * @dataProvider contentErrorProvider
     */
    public function serverErrorResponseProductionMessage(HttpMime $acceptMime): void
    {
        $serverRequest = $this->adapterFactory()
            ->createRequestFromGlobals()
            ->withAddedHeader('Accept', $acceptMime->value);

        $responseFactory = new HttpResponseFactory(
            $this->adapterFactory(),
            $serverRequest,
            Environment::PRODUCTION
        );

        $response = $responseFactory->serverErrorResponse(new Exception('monomo'));

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertStringNotContainsString("monomo", (string)$response->getBody());
        $this->assertStringContainsString("An error occurred on the server side", (string)$response->getBody());
        $this->assertTrue($response->hasHeader('Content-type'));
        $this->assertSame($acceptMime->value, $response->getHeaderLine('Content-type'));
    }
}
