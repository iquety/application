<?php

declare(strict_types=1);

namespace Tests\IoEngine\Console;

use Iquety\Application\IoEngine\Console\ConsoleBootstrap;
use Iquety\Injection\Container;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class ConsoleBootstrapTest extends TestCase
{
    /** @test */
    public function getters(): void
    {
        $bootstrap = new class extends ConsoleBootstrap
        {
            public function bootDependencies(Container $container): void {}

            public function getCommandName(): string { return ''; }

            /** Devolve o diretório real da aplicação que implementa o Console */
            public function getCommandPath(): string { return ''; }
        };

        $this->assertSame('', $bootstrap->getActionType());
        $this->assertSame('', $bootstrap->getErrorActionClass());
        $this->assertSame('', $bootstrap->getNotFoundActionClass());
        $this->assertSame('', $bootstrap->getMainActionClass());
    }
}
