<?php

declare(strict_types=1);

namespace Tests\IoEngine\Console\Stubs;

use Exception;
use Iquety\Application\IoEngine\Console\Script;
use Iquety\Console\Arguments;

class ExceptionRoutine extends Script
{
    protected function initialize(): void
    {
        $this->setName("test-exception");

        $this->setDescription("Exibe a mensagem 'olá' no terminal");

        $this->setHowToUse("./example dizer-ola [opcoes]");
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    protected function handle(Arguments $arguments): void
    {
        throw new Exception('Uma exceção foi lançada');
    }
}
