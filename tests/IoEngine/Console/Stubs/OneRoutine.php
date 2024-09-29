<?php

declare(strict_types=1);

namespace Tests\IoEngine\Console\Stubs;

use Iquety\Application\IoEngine\Console\Script;
use Iquety\Console\Arguments;

class OneRoutine extends Script
{
    protected function initialize(): void
    {
        $this->setName("one-routine");

        $this->setDescription("Exibe a mensagem 'olá' no terminal");

        $this->setHowToUse("./example dizer-ola [opcoes]");
    }

    protected function handle(Arguments $arguments): void
    {
        // dispara saída padrão para o teste capturar
        $this->line("Rotina de teste executada");

    }
}
