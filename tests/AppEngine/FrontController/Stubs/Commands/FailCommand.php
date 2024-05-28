<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController\Stubs\Commands;

use Exception;
use Iquety\Application\AppEngine\FrontController\Command\Command;
use Psr\Http\Message\ResponseInterface;

class FailCommand extends Command
{
    public function __construct()
    {
    }

    public function execute(): ResponseInterface
    {
        throw new Exception('failed command');
    }
}
