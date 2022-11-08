<?php

declare(strict_types=1);

namespace Tests\AppEngine\Mvc;

use ArrayObject;
use Exception;
use InvalidArgumentException;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Application\Bootstrap;
use Iquety\Application\Http\HttpStatus;
use Iquety\Routing\Router;
use Tests\AppEngine\Mvc\Support\Controllers\NoContractController;
use Tests\AppEngine\Mvc\Support\Controllers\UserController;
use Tests\TestCase;

class MvcOnlyExecuteTest extends TestCase
{
    // RuntimeException: This bootstrap has no routes registered
    // teste compartilhado em tests/AppEngine/EngineTestCase.php

    /** 
     * @test 
     * @dataProvider httpFactoryProvider
     */
    public function routeNotFound(string $httpFactoryContract): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, MvcEngine::class);
        $request = $this->requestFactory($httpFactory);

        /** @var InvocationMocker */
        $bootstrap = $this->createMock(MvcBootstrap::class);
        $bootstrap->method('bootRoutes')->will($this->returnCallback(function(Router $router){
            $router->get('/user/:id');
        }));
        
        /** @var MvcBootstrap $bootstrap */
        $engine->boot($bootstrap);

        $response = $engine->execute($request, [], fn() => null);

        $this->assertNull($response);
    }

    /** 
     * @test 
     * @dataProvider httpFactoryProvider
     */
    public function routeNoAction(string $httpFactoryContract): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, MvcEngine::class);
        $request = $this->requestFactory($httpFactory, 'user/42');

        /** @var InvocationMocker */
        $bootstrap = $this->createMock(MvcBootstrap::class);
        $bootstrap->method('bootRoutes')->will($this->returnCallback(function(Router $router){
            $router->get('/user/:id');
        }));
        
        /** @var MvcBootstrap $bootstrap */
        $engine->boot($bootstrap);

        $response = $engine->execute($request, [], fn() => null);

        $this->assertEquals(HttpStatus::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertStringContainsString(
            'Error: The route found does not have a action on file',
            (string)$response->getBody()
        );
    }

    /** 
     * @test 
     * @dataProvider httpFactoryProvider
     */
    public function bootModuleDependencies(string $httpFactoryContract): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, MvcEngine::class);
        $request = $this->requestFactory($httpFactory, 'user/42');

        /** @var InvocationMocker */
        $bootstrap = $this->createMock(MvcBootstrap::class);
        $bootstrap->method('bootRoutes')->will($this->returnCallback(function(Router $router){
            $router->get('/user/:id')
                ->usingAction(fn() => 'serve pra nada neste teste');
        }));

        /** @var MvcBootstrap $bootstrap */
        $engine->boot($bootstrap);

        $response = $engine->execute(
            $request,
            [$bootstrap::class => &$bootstrap],
            function(MvcBootstrap $bootstrap) {
                throw new Exception(
                    'Configuração das dependências efetuada'
                );
            }
        );

        $this->assertEquals(HttpStatus::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertStringContainsString(
            'Error: Configuração das dependências efetuada',
            (string)$response->getBody()
        );
    }

    /** 
     * @test 
     * @dataProvider httpFactoryProvider
     */
    public function controllerInvalidContract(string $httpFactoryContract): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, MvcEngine::class);
        $request = $this->requestFactory($httpFactory, 'user/42');

        /** @var InvocationMocker */
        $bootstrap = $this->createMock(MvcBootstrap::class);
        $bootstrap->method('bootRoutes')->will($this->returnCallback(function(Router $router){
            $router->get('/user/:id')
                ->usingAction(NoContractController::class . '::create');
        }));

        /** @var MvcBootstrap $bootstrap */
        $engine->boot($bootstrap);

        $response = $engine->execute(
            $request,
            [$bootstrap::class => &$bootstrap],
            function(MvcBootstrap $bootstrap) {}
        );

        $this->assertEquals(HttpStatus::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertStringContainsString(
            sprintf('Error: Class type %s is not allowed', NoContractController::class),
            (string)$response->getBody()
        );
    }

    /** 
     * @test 
     * @dataProvider httpFactoryProvider
     */
    public function controllerOk(string $httpFactoryContract): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, MvcEngine::class);
        $request = $this->requestFactory($httpFactory, 'user/42');

        /** @var InvocationMocker */
        $bootstrap = $this->createMock(MvcBootstrap::class);
        $bootstrap->method('bootRoutes')->will($this->returnCallback(function(Router $router){
            $router->get('/user/:id')
                ->usingAction(UserController::class . '::create');
        }));

        /** @var MvcBootstrap $bootstrap */
        $engine->boot($bootstrap);

        $response = $engine->execute(
            $request,
            [$bootstrap::class => &$bootstrap],
            function(MvcBootstrap $bootstrap) {}
        );

        $this->assertEquals(HttpStatus::HTTP_OK, $response->getStatusCode());
        $this->assertStringContainsString(
            sprintf('Resposta do controlador para id 42', UserController::class),
            (string)$response->getBody()
        );
    }
}
