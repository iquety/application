<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\Command\MainCommand;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\Command\Command;
use Iquety\Application\AppEngine\FrontController\Command\ErrorCommand;
use Iquety\Application\AppEngine\FrontController\Command\NotFoundCommand;
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
