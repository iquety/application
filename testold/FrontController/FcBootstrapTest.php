<?php

declare(strict_types=1);

namespace Tests\IoEngine\FrontController;

use Iquety\Application\IoEngine\FrontController\Command\MainCommand;
use Iquety\Application\IoEngine\FrontController\FcBootstrap;
use Iquety\Application\IoEngine\FrontController\Command\Command;
use Iquety\Application\IoEngine\FrontController\Command\ErrorCommand;
use Iquety\Application\IoEngine\FrontController\Command\NotFoundCommand;
use Iquety\Injection\Container;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class FcBootstrapTest extends TestCase
{
    /** @test */
    public function getters(): void
    {
        $bootstrap = new class extends FcBootstrap
        {
            public function bootDependencies(Container $container): void
            {

            }
        };

        $this->assertSame(Command::class, $bootstrap->getActionType());
        $this->assertSame(ErrorCommand::class, $bootstrap->getErrorActionClass());
        $this->assertSame(NotFoundCommand::class, $bootstrap->getNotFoundActionClass());
        $this->assertSame(MainCommand::class, $bootstrap->getMainActionClass());
    }
}
