<?php

declare(strict_types=1);

namespace Tests;

use Iquety\Application\ActionExecutor;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\ActionDescriptor;
use Iquety\Application\AppEngine\Mvc\Controller\Controller;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\Application;
use Iquety\Application\Environment;
use Iquety\Application\Http\HttpDependencies;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\Session;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tests\AppEngine\Mvc\Stubs\FailController;
use Tests\AppEngine\Mvc\Stubs\MakeableController;
use Tests\AppEngine\Mvc\Stubs\MethodPostController;
use Tests\AppEngine\Mvc\Stubs\OneController;

class ActionExecutorMvcTest extends TestCase
{
    /** @test */
    public function successResponse(): void
    {
        // o Controlador usa a instância da aplicação para fabricar 
        // as dependências, por isso não usa-se 'new Container' aqui
        $container = Application::instance()->container();

        $bootstrap = $this->makeMvcBootstrapWithDeps(
            $container,
            Input::fromString('')
        );

        $response = $this->makeExecutorResponse(
            $container,
            $bootstrap,
            MakeableController::class
        );

        // $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            'ok',
            (string)$response->getBody()
        );
    }

    // /** @test */
    // public function successResponseWithMakeable(): void
    // {
    //     $container = Application::instance()->container();

    //     $bootstrap = $this->makeBootstrap();
    //     $bootstrap->bootDependencies($container);

    //     $container->addSingleton(Input::class, Input::fromString('/fail/33?id=333'));
    //     (new HttpDependencies(Environment::DEVELOPMENT))->attachTo($container);

    //     $executor = new ActionExecutor(
    //         $container,
    //         $bootstrap->getActionType(),
    //         $bootstrap->getNotFoundActionClass(),
    //         $bootstrap->getErrorActionClass()
    //     );

    //     $response = $executor->makeResponseBy(
    //         new ActionDescriptor(
    //             Controller::class,
    //             $bootstrap::class,
    //             MakeableController::class,
    //             'execute'
    //         )
    //     );

    //     $this->assertSame(200, $response->getStatusCode());
    //     $this->assertSame(
    //         'ok',
    //         (string)$response->getBody()
    //     );
    // }

    // /** @test */
    // public function notFoundResponse(): void
    // {
    //     $container = new Container();

    //     $bootstrap = $this->makeBootstrap();
    //     $bootstrap->bootDependencies($container);

    //     $container->addSingleton(Input::class, Input::fromString('/fail/33?id=333'));
    //     (new HttpDependencies(Environment::DEVELOPMENT))->attachTo($container);

    //     $executor = new ActionExecutor(
    //         $container,
    //         $bootstrap->getActionType(),
    //         $bootstrap->getNotFoundActionClass(),
    //         $bootstrap->getErrorActionClass()
    //     );

    //     $response = $executor->makeResponseBy(
    //         new ActionDescriptor(
    //             Controller::class,
    //             $bootstrap::class,
    //             MethodPostController::class,
    //             'execute'
    //         )
    //     );

    //     // $this->assertSame(404, $response->getStatusCode());
    //     $this->assertSame(
    //         'Resposta do controlador para id 333 input 0=33&id=333',
    //         (string)$response->getBody()
    //     );
    // }

    // /** @test */
    // public function errorResponse(): void
    // {
    //     $container = new Container();

    //     $bootstrap = $this->makeBootstrap();
    //     $bootstrap->bootDependencies($container);

    //     $container->addSingleton(Input::class, Input::fromString('/fail/33'));
    //     (new HttpDependencies(Environment::DEVELOPMENT))->attachTo($container);

    //     $executor = new ActionExecutor(
    //         $container,
    //         $bootstrap->getActionType(),
    //         $bootstrap->getNotFoundActionClass(),
    //         $bootstrap->getErrorActionClass()
    //     );

    //     $response = $executor->makeResponseBy(
    //         new ActionDescriptor(
    //             Controller::class,
    //             $bootstrap::class,
    //             FailController::class,
    //             'execute'
    //         )
    //     );

    //     $this->assertSame(500, $response->getStatusCode());
    //     $this->assertSame('Test Error', (string)$response->getBody());
    // }

    private function makeMvcBootstrapWithDeps(
        Container $container,
        Input $input
    ): MvcBootstrap {
        $bootstrap = new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton(Session::class, MemorySession::class);
                $container->addSingleton(HttpFactory::class, new DiactorosHttpFactory());
            }

            public function bootRoutes(Router &$router): void {}
        };

        $bootstrap->bootDependencies($container);

        // aplica as dependências
        $container->addSingleton(Input::class, $input);

        // valida as dependências mínimas para HTTP e adiciona outras
        (new HttpDependencies(Environment::DEVELOPMENT))->attachTo($container);

        return $bootstrap;
    }

    private function makeExecutorResponse(
        Container $container,
        MvcBootstrap $mainBootstrap,
        string $controllerSignature
    ): ResponseInterface {
        // $mainBootstrap irá conter o primeiro módulo registrado no sistema
        // O MvcBoostrap oferece:
        // os Controller's para 404 e 500
        // o tipo 'Controller' para verificação das implementações
        $executor = new ActionExecutor($container, $mainBootstrap);

        // CASO DE SUCESSO
        // o $controllerSignature::execute é resolvido com sucesso
        // a resposta correspondente a $controllerSignature será devolvida

        // CASO DE MÉTODO INVÁLIDO (GET, POST, ETC)
        // o $controllerSignature::execute não pode ser resolvido
        // a resposta do $mainBootstrap::getNotFoundActionClass::execute será devolvida

        // CASO DE ERRO
        // o $controllerSignature::execute dispara uma exceção/erro
        // a resposta do $mainBootstrap::getErrorActionClass::execute será devolvida

        return $executor->makeResponseBy(
            // ActionDescriptor contém as informações necessárias para 
            // validar o tipo (Controller::class) 
            // e invocar a rotina ($controllerSignature::execute)
            // para devolver a resposta
            new ActionDescriptor(
                Controller::class, // o tipo de classe permitida
                $mainBootstrap::class, // para identificacao do módulo executado
                $controllerSignature, // o caminho completo até a classe
                'execute' // nos Controller's, o nome do método pode ser qualquer um
            )
        );
    }
}
