<?php

declare(strict_types=1);

namespace Tests\ActionExecutor;

use Iquety\Application\ActionExecutor;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Application\Http\HttpStatus;
use Iquety\Application\IoEngine\Action\ActionDescriptor;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;
use Iquety\Application\IoEngine\Mvc\Controller\ErrorController;
use Iquety\Application\IoEngine\Mvc\Controller\NotFoundController;
use Iquety\Application\IoEngine\Mvc\MvcModule;
use Tests\ActionExecutor\Stubs\AnyController;
use Tests\ActionExecutor\Stubs\ExceptionController;
use Tests\ActionExecutor\Stubs\MethodNotController;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.CouplingBetweenObjects) */
class ActionExecutorTest extends TestCase
{
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function makeResponseBy(): void
    {
        $container = $this->makeContainer();

        $container->addFactory(
            HttpResponseFactory::class,
            $this->makeResponseFactory()
        );

        $container->addFactory(
            Input::class,
            Input::fromRequest($this->makeServerRequest())
        );

        $module = $this->createMock(Module::class);

        /** @var Module $module */
        $executor = new ActionExecutor($container, $module);

        $response = $executor->makeResponseBy(new ActionDescriptor(
            Controller::class,
            $module::class,
            AnyController::class,
            'execute'
        ));

        $this->assertSame(HttpStatus::OK->value, $response->getStatusCode());
        $this->assertSame('Test', (string)$response->getBody());
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function methodNotAlowed(): void
    {
        $container = $this->makeContainer();

        $container->addFactory(
            HttpResponseFactory::class,
            $this->makeResponseFactory()
        );

        $container->addFactory(
            Input::class,
            Input::fromRequest($this->makeServerRequest())
        );

        $module = $this->createMock(MvcModule::class);

        $module->method('getNotFoundActionClass')
            ->willReturn(NotFoundController::class);

        /** @var Module $module */
        $executor = new ActionExecutor($container, $module);

        $response = $executor->makeResponseBy(new ActionDescriptor(
            Controller::class,
            $module::class,
            MethodNotController::class,
            'execute'
        ));

        $this->assertSame(HttpStatus::NOT_FOUND->value, $response->getStatusCode());
        $this->assertSame('Not Found', (string)$response->getBody());
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function actionError(): void
    {
        $container = $this->makeContainer();

        $container->addFactory(
            HttpResponseFactory::class,
            $this->makeResponseFactory()
        );

        $container->addFactory(
            Input::class,
            Input::fromRequest($this->makeServerRequest())
        );

        $module = $this->createMock(MvcModule::class);

        $module->method('getErrorActionClass')
            ->willReturn(ErrorController::class);

        /** @var Module $module */
        $executor = new ActionExecutor($container, $module);

        $response = $executor->makeResponseBy(new ActionDescriptor(
            Controller::class,
            $module::class,
            ExceptionController::class,
            'execute'
        ));

        $this->assertSame(HttpStatus::INTERNAL_SERVER_ERROR->value, $response->getStatusCode());
        $this->assertSame('Error message', (string)$response->getBody());
    }
}
