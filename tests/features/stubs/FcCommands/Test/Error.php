<?php

declare(strict_types=1);

namespace Tests\FcCommands;

use Exception;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\FrontController\Command;

class Error extends Command
{
    public function execute(Input $input): void
    {
        throw new Exception('Exceção lançada na execução do recurso solicitado');
    }
}

