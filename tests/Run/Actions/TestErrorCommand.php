<?php

declare(strict_types=1);

namespace Tests\Run\Actions;

use Exception;
use Iquety\Application\IoEngine\FrontController\Command\Command;

class TestErrorCommand extends Command
{
    public function execute(): string
    {
        throw new Exception('Mensagem de erro');
    }
}
