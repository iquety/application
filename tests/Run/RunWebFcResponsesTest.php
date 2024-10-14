<?php

declare(strict_types=1);

namespace Tests\Run;

use Iquety\Application\Environment;
use Iquety\Application\IoEngine\EngineSet;
use Iquety\Application\IoEngine\FrontController\FcEngine;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Application\RunWeb;
use Iquety\Http\Adapter\Session\NativeSession;
use Iquety\Http\HttpFactory;
use Iquety\Http\Session;
use Iquety\Injection\Container;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RunWebFcResponsesTest extends TestCase
{
    /** @test */
    public function response404(): void
    {
        $container = $this->makeContainer();

        // executa o motor Web
        $response = $this->makeResponse($container, '/not-found');

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('Not Found', (string)$response->getBody());
    }

    /** @test */
    public function response500(): void
    {
        $container = $this->makeContainer();

        // executa o motor Web
        $response = $this->makeResponse($container, '/test-error-command');

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('Mensagem de erro', (string)$response->getBody());
    }

    /** @test */
    public function responseMainDefault(): void
    {
        $container = $this->makeContainer();

        $response = $this->makeResponse($container, '/');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Iquety Framework - Home Page', (string)$response->getBody());
    }

    private function makeResponse(Container $container, string $uri, ?Module $extraModule = null): ResponseInterface
    {
        $factory = $this->makeHttpFactory();

        // disponibiliza as dependências obrigatórias
        $container->addFactory(Session::class, new NativeSession());
        $container->addFactory(HttpFactory::class, $factory);

        $originalRequest = $factory->createRequestFromGlobals();
        $originalRequest = $originalRequest->withUri($factory->createUri($uri));

        // executa o motor Web
        return $this->makeRunnner($container, $extraModule)
            ->run($originalRequest->withUri($factory->createUri($uri)));
    }

    private function makeRunnner(Container $container, ?Module $extraModule = null): RunWeb
    {
        $module = $this->makeFcModuleOne('Tests\Run\Actions');

        $moduleSet = new ModuleSet();
        $moduleSet->add($module);

        if ($extraModule !== null) {
            $moduleSet->add($extraModule);
        }

        $engine = new FcEngine();
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
