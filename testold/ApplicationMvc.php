<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\HttpFactory\GuzzleHttpFactory;
use Iquety\Application\Adapter\HttpFactory\NyHolmHttpFactory;
use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\Session;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use Tests\AppEngine\Mvc\Stubs\ArrayController;
use Tests\AppEngine\Mvc\Stubs\AssociativeController;
use Tests\AppEngine\Mvc\Stubs\OneController;
use Tests\AppEngine\Mvc\Stubs\ResponseController;
use Tests\AppEngine\Mvc\Stubs\StringController;

trait ApplicationMvc
{
    protected function makeMvcBootstrapOne(): MvcBootstrap
    {
        return new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('mvc-dep-one', fn() => 'one');
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/mvc-one/:id')->usingAction(OneController::class, 'execute');
            }
        };
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    protected function makeMvcBootstrapTwo(): MvcBootstrap
    {
        return new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('mvc-dep-two', fn() => 'two');
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/mvc-two/:id')->usingAction(OneController::class, 'execute');
            }
        };
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    protected function makeMvcBootstrapWithoutDependencies(): MvcBootstrap
    {
        return new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                //...
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/mvc-one/:id')->usingAction(OneController::class, 'execute');
            }
        };
    }

    protected function makeMvcBootstrapInvalidSession(): MvcBootstrap
    {
        return new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton(Session::class, fn() => (object)[]);
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/mvc-one/:id')->usingAction(OneController::class, 'execute');
            }
        };
    }

    protected function makeMvcBootstrapSession(): MvcBootstrap
    {
        return new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton(Session::class, MemorySession::class);
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/mvc-one/:id')->usingAction(OneController::class, 'execute');
            }
        };
    }

    protected function makeMvcBootstrapSessionInvalidHttpFactory(): MvcBootstrap
    {
        return new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton(Session::class, MemorySession::class);
                $container->addSingleton(HttpFactory::class, fn() => (object)[]);
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/mvc-one/:id')->usingAction(OneController::class, 'execute');
            }
        };
    }

    protected function makeMvcBootstrapSessionDiactoros(): MvcBootstrap
    {
        return new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton(Session::class, MemorySession::class);
                $container->addSingleton(HttpFactory::class, new DiactorosHttpFactory());
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/mvc-one/:id')->usingAction(OneController::class, 'execute');
            }
        };
    }

    protected function makeMvcBootstrapResponses(): MvcBootstrap
    {
        return new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton(Session::class, MemorySession::class);
                $container->addSingleton(HttpFactory::class, new DiactorosHttpFactory());
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/mvc-array/:id')->usingAction(ArrayController::class, 'execute');
                $router->get('/mvc-associative/:id')->usingAction(AssociativeController::class, 'execute');
                $router->get('/mvc-response/:id')->usingAction(ResponseController::class, 'execute');
                $router->get('/mvc-string/:id')->usingAction(StringController::class, 'execute');

                $router->post('/mvc-array/:id')->usingAction(ArrayController::class, 'execute');
                $router->post('/mvc-associative/:id')->usingAction(AssociativeController::class, 'execute');
                $router->post('/mvc-response/:id')->usingAction(ResponseController::class, 'execute');
                $router->post('/mvc-string/:id')->usingAction(StringController::class, 'execute');

                $router->put('/mvc-array/:id')->usingAction(ArrayController::class, 'execute');
                $router->put('/mvc-associative/:id')->usingAction(AssociativeController::class, 'execute');
                $router->put('/mvc-response/:id')->usingAction(ResponseController::class, 'execute');
                $router->put('/mvc-string/:id')->usingAction(StringController::class, 'execute');

                $router->patch('/mvc-array/:id')->usingAction(ArrayController::class, 'execute');
                $router->patch('/mvc-associative/:id')->usingAction(AssociativeController::class, 'execute');
                $router->patch('/mvc-response/:id')->usingAction(ResponseController::class, 'execute');
                $router->patch('/mvc-string/:id')->usingAction(StringController::class, 'execute');

                $router->delete('/mvc-array/:id')->usingAction(ArrayController::class, 'execute');
                $router->delete('/mvc-associative/:id')->usingAction(AssociativeController::class, 'execute');
                $router->delete('/mvc-response/:id')->usingAction(ResponseController::class, 'execute');
                $router->delete('/mvc-string/:id')->usingAction(StringController::class, 'execute');
            }
        };
    }

    protected function makeMvcBootstrapSessionGuzzle(): MvcBootstrap
    {
        return new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton(Session::class, MemorySession::class);
                $container->addSingleton(HttpFactory::class, new GuzzleHttpFactory());
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/mvc-one/:id')->usingAction(OneController::class, 'execute');
            }
        };
    }

    protected function makeMvcBootstrapSessionNyHolm(): MvcBootstrap
    {
        return new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton(Session::class, MemorySession::class);
                $container->addSingleton(HttpFactory::class, new NyHolmHttpFactory());
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/mvc-one/:id')->usingAction(OneController::class, 'execute');
            }
        };
    }

    protected function makeMvcBootstrapException(): MvcBootstrap
    {
        return new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton(Session::class, MemorySession::class);
                $container->addSingleton(HttpFactory::class, new NyHolmHttpFactory());
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/mvc-one/:id')->usingAction(OneController::class, 'execute');
            }

            public function getErrorActionClass(): string
            {
                throw new Exception('Proposital exception');
            }
        };
    }
}
