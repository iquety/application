<?php

declare(strict_types=1);

namespace Tests\AppEngine\Console\Stubs;

use Exception;
use Iquety\Application\AppEngine\Console\Script;
use Iquety\Console\Arguments;

class ExceptionRoutine extends Script
{
    protected function initialize(): void
    {
        $this->setName("test-exception");

        $this->setDescription("Exibe a mensagem 'olá' no terminal");

        $this->setHowToUse("./example dizer-ola [opcoes]");
    }

    protected function handle(Arguments $arguments): void
    {
        throw new Exception('Uma exceção foi lançada');
    }
}
