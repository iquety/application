<?php

declare(strict_types=1);

namespace Tests\IoEngine\Mvc;

use Iquety\Application\IoEngine\Mvc\Controller\Controller;
use Iquety\Application\IoEngine\Mvc\Controller\ErrorController;
use Iquety\Application\IoEngine\Mvc\Controller\MainController;
use Iquety\Application\IoEngine\Mvc\Controller\NotFoundController;
use Iquety\Application\IoEngine\Mvc\MvcModule;
use Iquety\Injection\Container;
use Tests\TestCase;

class MvcModuleTest extends TestCase
{
    /** @test */
    public function getters(): void
    {
        $module = $this->makeModule();

        $this->assertSame(Controller::class, $module->getActionType());
        $this->assertSame(ErrorController::class, $module->getErrorActionClass());
        $this->assertSame(MainController::class, $module->getMainActionClass());
        $this->assertSame(NotFoundController::class, $module->getNotFoundActionClass());
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    private function makeModule(): MvcModule
    {
        return new class extends MvcModule
        {
            public function bootDependencies(Container $container): void
            {
            }
        };
    }
}
