<?php

declare(strict_types=1);

namespace Tests\Unit;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\AppEngine\EngineSet;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\FrontController\Source;
use Iquety\Application\AppEngine\FrontController\SourceSet;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpMethod;
use Iquety\Application\Http\HttpMime;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;
use Tests\Unit\AppEngine\Mvc\Stubs\OneController;
use Tests\Unit\TestCase;

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
