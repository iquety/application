<?php

declare(strict_types=1);

namespace Tests;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpMethod;
use Iquety\Application\Http\HttpMime;
use Psr\Http\Message\ServerRequestInterface;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class ApplicationCase extends TestCase
{
    use ApplicationFc;
    use ApplicationMvc;

    public function setUp(): void
    {
        Application::instance()->reset();
    }

    public function tearDown(): void
    {
        Application::instance()->reset();
    }

    protected function makeServerRequest(
        string $path = '',
        HttpMethod $method = HttpMethod::ANY,
        HttpMime $acceptMimeType = HttpMime::HTML
    ): ServerRequestInterface {
        $httpFactory = new DiactorosHttpFactory();

        $request = $httpFactory->createRequestFromGlobals();

        $request = $request->withAddedHeader('Accept', $acceptMimeType->value);

        if ($path === '') {
            return $request;
        }

        if ($method !== HttpMethod::ANY) {
            $request = $request->withMethod($method->value);
        }

        return $request->withUri(
            $httpFactory->createUri("http://localhost/" . trim($path, '/'))
        );
    }
}
