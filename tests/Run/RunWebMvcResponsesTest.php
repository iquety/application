<?php

declare(strict_types=1);

namespace Tests\Run;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\Session\SymfonyNativeSession;
use Iquety\Application\Application;
use Iquety\Application\Environment;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\Session;
use Iquety\Application\IoEngine\EngineSet;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Iquety\Application\IoEngine\Mvc\MvcModule;
use Iquety\Application\RunWeb;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Tests\Run\Actions\TestErrorController;
use Tests\Run\Actions\TestMainController;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class RunWebMvcResponsesTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    public function tearDown(): void
    {
        Application::instance()->reset();
    }
    
    /**
     * o container é obtido da aplicação dentro dos controladores
     * por isso deve ser manipulado aqui através dela
     */
    private function makeContainer(): Container
    {
        return Application::instance()->container();
    }

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
        $response = $this->makeResponse($container, '/error');

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

    /** @test */
    public function responseMainWithRoute(): void
    {
        $container = $this->makeContainer();

        $extraModule = new class extends MvcModule
        {
            public function bootDependencies(Container $container): void
            {
                // ...
            }

            public function bootRoutes(Router &$router): void
            {
                // adicionada a rota '/' para forçar a resolução pelo roteador
                $router->get('/')
                    ->usingAction(TestMainController::class, 'myMethod');
            }
        };

        $response = $this->makeResponse($container, '/', $extraModule);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Custom home page', (string)$response->getBody());
    }

    private function makeResponse(Container $container, string $uri, ?Module $extraModule = null): ResponseInterface
    {
        // disponibiliza as dependências obrigatórias
        $container->addFactory(Session::class, new SymfonyNativeSession());
        $container->addFactory(HttpFactory::class, new DiactorosHttpFactory());

        /** @var DiactorosHttpFactory $factory */
        $factory = $container->get(HttpFactory::class);

        $originalRequest = $factory->createRequestFromGlobals();

        $originalRequest = $originalRequest->withUri($factory->createUri($uri));

        // executa o motor Web
        return $this->makeRunnner($container, $extraModule)
            ->run($originalRequest->withUri($factory->createUri($uri)));
    }

    private function makeRunnner(Container $container, ?Module $extraModule = null): RunWeb
    {
        $module = new class extends MvcModule
        {
            public function bootDependencies(Container $container): void
            {
                // ...
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/error')
                    ->usingAction(TestErrorController::class, 'myMethod');   
            }
        };

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
