<?php

declare(strict_types=1);

namespace Tests\Run;

use Iquety\Application\Environment;
use Iquety\Application\IoEngine\EngineSet;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Iquety\Application\RunWeb;
use Iquety\Http\Adapter\Session\MemorySession;
use Iquety\Http\Adapter\Session\NativeSession;
use Iquety\Http\HttpFactory;
use Iquety\Http\HttpMethod;
use Iquety\Http\Session;
use Iquety\Injection\Container;
use Psr\Http\Message\ResponseInterface;
use Tests\Run\Actions\TestErrorController;
use Tests\Run\Actions\TestFlashErrorController;
use Tests\Run\Actions\TestMainController;
use Tests\Run\Actions\TestValidationErrorController;
use Tests\Run\Actions\TestValidationOkController;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RunWebMvcResponsesTest extends TestCase
{
    /** @test */
    public function response404(): void
    {
        $container = $this->makeContainer();

        // executa o motor Web
        $response = $this->makeResponse($container, '/not-found');

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('"Not Found"', (string)$response->getBody());
    }

    /** @test */
    public function response500(): void
    {
        $container = $this->makeContainer();

        // executa o motor Web
        $response = $this->makeResponse($container, '/error');

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('"Mensagem de erro"', (string)$response->getBody());
    }

    /** @test */
    public function responseMainDefault(): void
    {
        $container = $this->makeContainer();

        $response = $this->makeResponse($container, '/');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('"Iquety Framework - Home Page"', (string)$response->getBody());
    }

    /** @test */
    public function responseMainWithRoute(): void
    {
        $container = $this->makeContainer();

        $extraModule = $this->makeMvcModuleTwo(
            HttpMethod::GET,
            '/',
            TestMainController::class . '@myMethod'
        );

        $response = $this->makeResponse($container, '/', $extraModule);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('"Custom home page"', (string)$response->getBody());
    }

    /** @test */
    public function responseFlashError(): void
    {
        $container = $this->makeContainer();

        $extraModule = $this->makeMvcModuleTwo(
            HttpMethod::GET,
            '/',
            TestFlashErrorController::class . '@myMethod'
        );

        $response = $this->makeResponse($container, '/', $extraModule);

        $this->assertSame('/destination', $response->getHeaderLine('Location'));

        $this->assertSame('', (string)$response->getBody());

        $this->assertSame(400, $response->getStatusCode());
    }

    /** @test */
    public function responseValidationError(): void
    {
        $container = $this->makeContainer();

        $extraModule = $this->makeMvcModuleTwo(
            HttpMethod::GET,
            '/',
            TestValidationErrorController::class . '@myMethod'
        );

        $response = $this->makeResponse($container, '/', $extraModule);

        $expected = json_encode([
            'name' => ["Value of the field 'name' must be equal to 'Ricardo Pereira'"],
            'email' => []
        ]);

        $this->assertSame($expected, (string)$response->getBody());

        $this->assertSame(400, $response->getStatusCode());
    }

    /** @test */
    public function responseValidationOk(): void
    {
        $container = $this->makeContainer();

        $extraModule = $this->makeMvcModuleTwo(
            HttpMethod::GET,
            '/',
            TestValidationOkController::class . '@myMethod'
        );

        $response = $this->makeResponse($container, '/', $extraModule);

        $this->assertSame('"ok"', (string)$response->getBody());

        $this->assertSame(200, $response->getStatusCode());
    }

    private function makeResponse(Container $container, string $uri, ?Module $extraModule = null): ResponseInterface
    {
        $factory = $this->makeHttpFactory();

        // disponibiliza as dependências obrigatórias
        $container->addFactory(Session::class, new MemorySession());
        $container->addFactory(HttpFactory::class, $factory);

        $originalRequest = $factory->createRequestFromGlobals();

        // imita os parâmetros POST
        $originalRequest = $originalRequest->withParsedBody([
            'name' => 'Ricardo',
            'email' => 'ricardo@naitis.com',
            'password' => '23456789'
        ]);

        $originalRequest = $originalRequest->withHeader('Accept', 'application/json');

        // executa o motor Web
        return $this->makeRunnner($container, $extraModule)
            ->run($originalRequest->withUri($factory->createUri($uri)));
    }

    private function makeRunnner(Container $container, ?Module $extraModule = null): RunWeb
    {
        $module = $this->makeMvcModuleOne(
            HttpMethod::GET,
            '/error',
            TestErrorController::class . '@myMethod'
        );

        $moduleSet = new ModuleSet();
        $moduleSet->add($module);

        if ($extraModule !== null) {
            $moduleSet->add($extraModule);
        }

        $engine = new MvcEngine();
        $engine->useModuleSet($moduleSet);

        $engineSet = new EngineSet($container);
        $engineSet->add($engine);
        $engineSet->bootEnginesWith($module);

        if ($extraModule !== null) {
            $engineSet->bootEnginesWith($extraModule);
        }

        $runner = new RunWeb(
            Environment::DEVELOPMENT,
            $container,
            $module,
            $engineSet
        );

        return $runner;
    }
}
