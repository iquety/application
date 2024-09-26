<?php

declare(strict_types=1);

namespace Tests\FcEngine;

use Iquety\Application\IoEngine\FrontController\Command\Command;
use Iquety\Application\IoEngine\FrontController\Command\ErrorCommand;
use Iquety\Application\IoEngine\FrontController\Command\MainCommand;
use Iquety\Application\IoEngine\FrontController\Command\NotFoundCommand;
use Iquety\Application\IoEngine\FrontController\FcModule;
use Iquety\Injection\Container;
use Tests\TestCase;

class FcModuleTest extends TestCase
{
    /** @test */
    public function getters(): void
    {
        $module = $this->makeModule();

        $this->assertSame(Command::class, $module->getActionType());
        $this->assertSame(ErrorCommand::class, $module->getErrorActionClass());
        $this->assertSame(MainCommand::class, $module->getMainActionClass());
        $this->assertSame(NotFoundCommand::class, $module->getNotFoundActionClass());
    }

    private function makeModule(): FcModule
    {
        return new class extends FcModule
        {
            public function bootDependencies(Container $container): void
            {
            }
        };
    }
}

