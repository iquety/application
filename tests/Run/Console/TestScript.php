<?php

declare(strict_types=1);

namespace Tests\Run\Console;

use Iquety\Application\IoEngine\Console\Script;
use Iquety\Console\Arguments;

class TestScript extends Script
{
    protected function initialize(): void
    {
        $this->setName('test-console');
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    protected function handle(Arguments $arguments): void
    {
        $this->info('Teste console');
    }
}
