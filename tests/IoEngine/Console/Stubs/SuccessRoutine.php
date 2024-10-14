<?php

declare(strict_types=1);

namespace Tests\IoEngine\Console\Stubs;

use Iquety\Application\IoEngine\Console\ConsoleRoutine;
use Iquety\Console\Arguments;

class SuccessRoutine extends ConsoleRoutine
{
    protected function initialize(): void
    {
        $this->setName("test-success");

        $this->setDescription("Exibe a mensagem 'olá' no terminal");

        $this->setHowToUse("./example dizer-ola [opcoes]");
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    protected function handle(Arguments $arguments): void
    {
        // dispara saída padrão para o teste capturar
        $this->line("Rotina de teste executada");
    }
}
