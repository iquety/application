<?php

declare(strict_types=1);

namespace Tests\Run\Console;

use Exception;
use Iquety\Application\IoEngine\Console\Script;
use Iquety\Console\Arguments;

class ErrorScript extends Script
{
    protected function initialize(): void
    {
        $this->setName('test-error');
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    protected function handle(Arguments $arguments): void
    {
        throw new Exception('Console error');
    }
}
