<?php

declare(strict_types=1);

namespace Tests\IoEngine\Console\Stubs\SubDirectory;

use Iquety\Application\IoEngine\Console\Script;
use Iquety\Console\Arguments;

class TwoRoutine extends Script
{
    protected function initialize(): void
    {
        $this->setName("two-routine");

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
