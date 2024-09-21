<?php

declare(strict_types=1);

namespace Tests\AppEngine\Console\Stubs;

use Iquety\Application\AppEngine\Console\Script;
use Iquety\Console\Arguments;

class TwoRoutine extends Script
{
    protected function initialize(): void
    {
        $this->setName("two-routine");

        $this->setDescription("Exibe a mensagem 'olá' no terminal");

        $this->setHowToUse("./example dizer-ola [opcoes]");
    }

    protected function handle(Arguments $arguments): void
    {
        // dispara saída padrão para o teste capturar
        $this->line("Rotina de teste executada");

    }
}
